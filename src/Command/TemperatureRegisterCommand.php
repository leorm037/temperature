<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\City;
use App\Entity\Configuration;
use App\Factory\TemperatureFactory;
use App\Helper\ClimaTempoHelper;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\TemperatureRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
            name: 'temperature:register',
            description: 'Records CPU temperature, GPU and Weather Weather.',
    )]
class TemperatureRegisterCommand extends Command
{

    public const URL_WEATHER = 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/';
    private const TEMP_CPU_COMMAND = 'cat /sys/class/thermal/thermal_zone0/temp';
    private const TEMP_GPU_COMMAND = '/usr/bin/vcgencmd measure_temp';

    private KernelInterface $kernel;
    private ClimaTempoHelper $climaTempoHelper;
    private CityRepository $cityRepository;
    private TemperatureFactory $temperatureFactory;
    private TemperatureRepository $temperatureRepository;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            KernelInterface $kernel,
            ClimaTempoHelper $climaTempoHelper,
            CityRepository $cityRepository,
            TemperatureFactory $temperatureFactory,
            TemperatureRepository $temperatureRepository,
            ConfigurationRepository $configurationRepository
    )
    {
        parent::__construct();

        $this->kernel = $kernel;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->cityRepository = $cityRepository;
        $this->temperatureFactory = $temperatureFactory;
        $this->temperatureRepository = $temperatureRepository;
        $this->configurationRepository = $configurationRepository;
    }

    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $temperature = $this->temperatureFactory->build($this->climaTempo());

        $temperature
                ->setCpu($this->cpu())
                ->setGpu($this->gpu());
       
        $this->temperatureRepository->save($temperature, true);

        $io->title('Temperature');
        $io->text('Cidade:      ' . $temperature->getCity()->getName() . '/' . $temperature->getCity()->getState() . ' - ' . $temperature->getCity()->getCountry());
        $io->text('CPU:         ' . $temperature->getCpu());
        $io->text('GPU:         ' . $temperature->getGpu());
        $io->text('Temperatura: ' . $temperature->getTemperature());
        $io->text('Sensação:    ' . $temperature->getSensation());
        $io->text('Humidade:    ' . $temperature->getHumidity());
        $io->text('Pressão:     ' . $temperature->getPressure());
        $io->text('Velocidade:  ' . $temperature->getWindVelocity());
        $io->text('Direção:     ' . $temperature->getWindDirection());
        $io->text('Data:        ' . $temperature->getDateTime()->format('d/m/Y H:i:s'));
        $io->newLine();

        return Command::SUCCESS;
    }

    private function climaTempo(): array
    {
        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        /** @var City $city */
        $city = $this->cityRepository->listCitySelected()[0];

        if ($this->climaTempoHelper->getError()) {
            return null;
        }
        
        return $this->climaTempoHelper->weather($city->getId(), $token->getParamValue());
    }

    private function cpu()
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            return floatval(random_int(31, 35));
        }

        $tempCpu = shell_exec(self::TEMP_CPU_COMMAND);

        return $tempCpu / 1000;
    }

    private function gpu()
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            return floatval(random_int(30, 34));
        }

        $tempGpu = shell_exec(self::TEMP_GPU_COMMAND);

        return str_replace(['temp=', "'C"], '', $tempGpu);
    }
}
