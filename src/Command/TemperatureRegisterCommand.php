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
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
            name: 'temperature:register',
            description: 'Records CPU temperature, GPU and Weather Weather.',
    )]
class TemperatureRegisterCommand extends Command
{

    public const URL_WEATHER = 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/';
    private const TEMP_CPU_COMMAND = 'cat /sys/class/thermal/thermal_zone0/temp';
    private const TEMP_GPU_COMMAND = '/usr/bin/vcgencmd measure_temp';
    private const TEMP_PCSENSOR_TEMPER_COMMAND = 'sudo pcsensor -c -p';

    private KernelInterface $kernel;
    private LoggerInterface $logger;
    private ClimaTempoHelper $climaTempoHelper;
    private CityRepository $cityRepository;
    private MessageBusInterface $messageBus;
    private TemperatureFactory $temperatureFactory;
    private TemperatureRepository $temperatureRepository;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            KernelInterface $kernel,
            LoggerInterface $logger,
            ClimaTempoHelper $climaTempoHelper,
            CityRepository $cityRepository,
            MessageBusInterface $messageBus,
            TemperatureFactory $temperatureFactory,
            TemperatureRepository $temperatureRepository,
            ConfigurationRepository $configurationRepository,
    )
    {
        parent::__construct();

        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->cityRepository = $cityRepository;
        $this->messageBus = $messageBus;
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

        try {
            $temperature = $this->temperatureFactory->build($this->climaTempo());

            $temperature
                    ->setCpu($this->cpu())
                    ->setGpu($this->gpu())
                    ->setAmbiente($this->pcsensor())
            ;

            $this->temperatureRepository->save($temperature, true);

            $io->title('Temperature');

            if (null !== $temperature->getCity()) {
                $io->text('Cidade:      ' . $temperature->getCity()->getName() . '/' . $temperature->getCity()->getState() . ' - ' . $temperature->getCity()->getCountry());
            }

            $io->text('CPU:         ' . $temperature->getCpu());
            $io->text('GPU:         ' . $temperature->getGpu());
            $io->text('Ambiente:    ' . $temperature->getAmbiente());

            if (null !== $temperature->getTemperature()) {
                $io->text('Temperatura: ' . $temperature->getTemperature());
                $io->text('Sensação:    ' . $temperature->getSensation());
                $io->text('Humidade:    ' . $temperature->getHumidity());
                $io->text('Pressão:     ' . $temperature->getPressure());
                $io->text('Velocidade:  ' . $temperature->getWindVelocity());
                $io->text('Direção:     ' . $temperature->getWindDirection());
                $io->text('Data:        ' . $temperature->getDateTime()->format('d/m/Y H:i:s'));
            }

            $io->newLine();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->title('Error');
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    private function climaTempo(): ?array
    {
        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        /** @var City $city */
        $city = $this->cityRepository->listCitySelected();

        $weather = $this->climaTempoHelper->weather($city->getId(), $token->getParamValue());

        return $weather;
    }

    private function cpu()
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            return floatval(random_int(31, 35));
        }

        $tempCpu = shell_exec(self::TEMP_CPU_COMMAND);

        return $tempCpu / 1000;
    }

    private function gpu(): float
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            return floatval(random_int(30, 34));
        }

        $tempGpu = shell_exec(self::TEMP_GPU_COMMAND);

        $temp = str_replace(['temp=', "'C"], '', $tempGpu);

        return floatval($temp);
    }

    private function pcsensor(): float
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            return floatval(random_int(28, 33));
        }

        $tempPcsensor = shell_exec(self::TEMP_PCSENSOR_TEMPER_COMMAND);

        return floatval($tempPcsensor);
    }
}
