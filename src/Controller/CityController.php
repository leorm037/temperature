<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Factory\CityFactory;
use App\Form\CityConfigurationFormType;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityController extends AbstractController
{

    private CityRepository $cityRepository;
    private ConfigurationRepository $configurationRepository;
    private TranslatorInterface $translator;

    public function __construct(
            CityRepository $cityRepository,
            ConfigurationRepository $configurationRepository,
            TranslatorInterface $translator
    )
    {
        $this->cityRepository = $cityRepository;
        $this->configurationRepository = $configurationRepository;
        $this->translator = $translator;
    }

    public function index(Request $request): Response
    {
        $cityConfiguration = $this->configurationRepository->findByName('city');

        if (null == $cityConfiguration) {
            $cityConfiguration = new Configuration();
            $cityConfiguration->setParamName('city');
            $cityConfiguration->setParamValue(0);
            $this->configurationRepository->save($cityConfiguration, true);
        }

        $form = $this->createForm(CityConfigurationFormType::class, $cityConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->configurationRepository->save($cityConfiguration, true);

            $this->addFlash('success', '');

            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city/index.html.twig', [
                    'city' => $cityConfiguration,
                    'form' => $form,
        ]);
    }

    private function getCityClimaTempo()
    {
        $citiesJson = file_get_contents("http://apiadvisor.climatempo.com.br/api/v1/locale/city?country=BR&token=3a13c12183f4ff8f27f0579b3bae28ce");
        $cities = json_decode($citiesJson);

        $citiesCount = count($cities);

        for ($i = 0; $i < $citiesCount; $i++) {
            $city = CityFactory::build($cities[$i]);
            $this->cityRepository->save($city, ($i == $citiesCount - 1) ? true : false);
        }
    }
}
