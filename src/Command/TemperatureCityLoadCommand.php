<?php

namespace App\Command;

use App\Entity\Configuration;
use App\Factory\CityFactory;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
            name: 'temperature:city:load',
            description: 'Add a short description for your command',
    )]
class TemperatureCityLoadCommand extends Command
{

    const URL_CITY = "http://apiadvisor.climatempo.com.br/api/v1/locale/city?country=BR&token=";

    private CityRepository $cityRepository;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            CityRepository $cityRepository,
            ConfigurationRepository $configurationRepository
    )
    {
        parent::__construct();

        $this->cityRepository = $cityRepository;
        $this->configurationRepository = $configurationRepository;
    }

    protected function configure(): void
    {
        $this
                ->addArgument('country', InputArgument::REQUIRED, 'Country abbreviation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $country = strtoupper($input->getArgument('country'));

        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(ConfigurationRepository::CONFIGURATION_TOKEN);

        if (null != $token && null != $token->getParamValue()) {
            $url = self::URL_CITY . $token->getParamValue();
            $citiesJson = file_get_contents($url);
            $cities = json_decode($citiesJson);

            $citiesCount = count($cities);

            for ($i = 0; $i < $citiesCount; $i++) {
                $city = CityFactory::build($cities[$i]);
                $flush = ($citiesCount - 1 == $i) ? true : false;

                $this->cityRepository->save($city, $flush);
            }

            $io->success($citiesCount);

            return Command::SUCCESS;
        }
        
        $io->error("Token não cadastrado");

        return Command::FAILURE;
    }
}
