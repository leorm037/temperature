<?php

namespace App\Command;

use App\Factory\TemperatureFactory;
use App\Repository\TemperatureRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
            name: 'temperature:register',
            description: 'Records CPU temperature, GPU and Weather Weather.',
    )]
class TemperatureRegisterCommand extends Command
{

    private const TEMP_CPU_COMMAND = 'cat /sys/class/thermal/thermal_zone0/temp';
    private const TEMP_GPU_COMMAND = '/usr/bin/vcgencmd measure_temp';

    private string $urlApiTokenClimaTempo;
    private KernelInterface $kernel;
    private TemperatureRepository $temperatureRepository;

    public function __construct(
            KernelInterface $kernel,
            TemperatureRepository $temperatureRepository,
            $urlApiTokenClimaTempo
    )
    {
        $this->temperatureRepository = $temperatureRepository;
        $this->kernel = $kernel;
        $this->urlApiTokenClimaTempo = $urlApiTokenClimaTempo;
        parent::__construct();
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
        $tempClimaTempoJson = file_get_contents($this->urlApiTokenClimaTempo . "&" . rand());
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
