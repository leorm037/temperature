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

use App\Entity\Configuration;
use App\Factory\CityFactory;
use App\Helper\ClimaTempoHelper;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
            name: 'temperature:city:load',
            description: 'Loads the list of cities from the abbreviation of the informed country.',
    )]
class TemperatureCityLoadCommand extends Command
{

    private CityRepository $cityRepository;
    private ClimaTempoHelper $climaTempoHelper;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            CityRepository $cityRepository,
            ClimaTempoHelper $climaTempoHelper,
            ConfigurationRepository $configurationRepository
    ) {
        parent::__construct();

        $this->cityRepository = $cityRepository;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->configurationRepository = $configurationRepository;
    }

    protected function configure(): void
    {
        $this->addArgument('country', InputArgument::REQUIRED, 'Country abbreviation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $country = strtoupper($input->getArgument('country'));

        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        if (null == $token && null !== $token->getParamValue()) {
            $io->error('Token não cadastrado');
            return Command::FAILURE;
        }
        
        $citiesJson = $this->climaTempoHelper->findCities($country, $token);
        
        if ($this->climaTempoHelper->getError()) {
            $io->error($this->climaTempoHelper->getError());
            return Command::FAILURE;
        }
        
        $cities = json_decode($citiesJson);

        $citiesCount = count($cities);

        for ($i = 0; $i < $citiesCount; ++$i) {
            $city = CityFactory::build($cities[$i]);
            $flush = ($citiesCount - 1 == $i) ? true : false;

            $this->cityRepository->save($city, $flush);
        }

        $io->success($citiesCount);

        return Command::SUCCESS;
    }
}
