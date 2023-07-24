<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Configuration;
use App\Factory\CityFactory;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityConfigurationController extends AbstractController
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
        /** @var Configuration $cityConfiguration */
        $cityConfiguration = $this->configurationRepository->findByName('city');
        
        if (null == $cityConfiguration) {
            $cityConfiguration = new Configuration();
            $cityConfiguration->setParamName('city');
            $this->configurationRepository->save($cityConfiguration, true);
        }
        
        $countries = $this->cityRepository->listCountry();

        $city = new City();
        $states = [];
        $cities = [];

        if (null != $cityConfiguration->getParamValue()) {
            $city = $this->cityRepository->find($cityConfiguration->getParamValue());
            $states = $this->cityRepository->listStateFromCountry($city->getCountry());
            $cities = $this->cityRepository->listCityFromState($city->getState());
        }

        if ('GET' === $request->getMethod()) {
            return $this->render('cityConfiguration/index.html.twig', [
                        'city' => $city,
                        'countries' => $countries,
                        'states' => $states,
                        'cities' => $cities,
            ]);
        }

        $countryPost = $request->request->get('country');
        $statePost = $request->request->get('state');
        $cityPost = $request->request->get('city');

        $cityDatabase = $this->cityRepository->findByCountryStateIdCity($countryPost, $statePost, $cityPost);

        if (null == $cityDatabase) {
            $this->addFlash('danger', $this->translator->trans('message.cityConfiguration.update.error'));

            return $this->render('cityConfiguration/index.html.twig', [
                        'city' => $city,
                        'countries' => $countries,
                        'states' => $states,
                        'cities' => $cities,
            ]);
        }

        $cityConfiguration->setParamValue($cityDatabase->getId());

        $this->configurationRepository->save($cityConfiguration, true);

        $this->addFlash('success', $this->translator->trans('message.cityConfiguration.update.success'));

        return $this->redirectToRoute('app_city_configuration_index', [], Response::HTTP_SEE_OTHER);
    }

    public function stateJson(Request $request): JsonResponse
    {
        $country = $request->request->get('countryAbbreviation');

        $states = $this->cityRepository->listStateFromCountry($country);

        $message = (null != $states) ? 'success' : 'fail';

        return $this->json(['message' => $message, 'states' => $states], 200);
    }

    public function cityJson(Request $request): JsonResponse
    {
        $state = $request->request->get('stateAbbreviation');

        $cities = $this->cityRepository->listCityFromState($state);

        $message = (null != $cities) ? 'success' : 'fail';

        return $this->json(['message' => $message, 'cities' => $cities], 200);
    }

    private function getCityClimaTempo()
    {
        $citiesJson = file_get_contents("http://apiadvisor.climatempo.com.br/api/v1/locale/city?country=BR&token=token");
        $cities = json_decode($citiesJson);

        $citiesCount = count($cities);

        for ($i = 0; $i < $citiesCount; $i++) {
            $city = CityFactory::build($cities[$i]);
            $this->cityRepository->save($city, ($i == $citiesCount - 1) ? true : false);
        }
    }
}
