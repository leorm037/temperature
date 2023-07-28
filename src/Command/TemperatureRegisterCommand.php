<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Factory\TemperatureFactory;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\TemperatureRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extra\TwigExtraBundle\DependencyInjection\Configuration as Configuration2;

#[AsCommand(
            name: 'temperature:register',
            description: 'Records CPU temperature, GPU and Weather Weather.',
    )]
class TemperatureRegisterCommand extends Command
{
    const URL_WEATHER = "http://apiadvisor.climatempo.com.br/api/v1/weather/locale/";
    private const TEMP_CPU_COMMAND = 'cat /sys/class/thermal/thermal_zone0/temp';
    private const TEMP_GPU_COMMAND = '/usr/bin/vcgencmd measure_temp';

    private KernelInterface $kernel;
    private CityRepository $cityRepository;
    private TemperatureRepository $temperatureRepository;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            KernelInterface $kernel,
            CityRepository $cityRepository,
            TemperatureRepository $temperatureRepository,
            ConfigurationRepository $configurationRepository
    ) {
        parent::__construct();

        $this->kernel = $kernel;
        $this->cityRepository = $cityRepository;
        $this->temperatureRepository = $temperatureRepository;
        $this->configurationRepository = $configurationRepository;
    }

    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $temperature = TemperatureFactory::build($this->climaTempo());

        $temperature
                ->setCpu($this->cpu())
                ->setGpu($this->gpu());

        $this->temperatureRepository->save($temperature, true);

        $io->title("Temperature");
        $io->text("CPU:         " . $temperature->getCpu());
        $io->text("GPU:         " . $temperature->getGpu());
        $io->text("Temperatura: " . $temperature->getTemperature());
        $io->text("Sensação:    " . $temperature->getSensation());
        $io->text("Humidade:    " . $temperature->getHumidity());
        $io->text("Pressão:     " . $temperature->getPressure());
        $io->text("Velocidade:  " . $temperature->getWindVelocity());
        $io->text("Direção:     " . $temperature->getWindDirection());
        $io->text("Data:        " . $temperature->getDateTime()->format('d/m/Y H:i:s'));
        $io->newLine();

        return Command::SUCCESS;
    }

    private function climaTempo(): array
    {
        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);
        
        $city = $this->cityRepository->listCitySelected()[0];
        
        $url = self::URL_WEATHER . $city->getId() . "/current?token=" . $token->getParamValue() . "&salt=" . rand();
        
        dd($url);
        
        $tempClimaTempoJson = file_get_contents($url);
        $data = json_decode($tempClimaTempoJson, true);
        $climaTempo = $data['data'];

        return $climaTempo;
    }

    private function cpu()
    {
        if ("dev" === $this->kernel->getEnvironment()) {
            return floatval(random_int(31, 35));
        }
        
        $tempCpu = shell_exec(self::TEMP_CPU_COMMAND);

        return $tempCpu / 1000;
    }

    private function gpu()
    {
        if ("dev" === $this->kernel->getEnvironment()) {
            return floatval(random_int(30, 34));
        }
        
        $tempGpu = shell_exec(self::TEMP_GPU_COMMAND);

        return str_replace(['temp=', "'C"], '', $tempGpu);
    }

}
