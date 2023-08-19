<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\City;
use App\Entity\Configuration;
use App\Helper\ClimaTempoHelper;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityController extends AbstractController
{

    private CityRepository $cityRepository;
    private TranslatorInterface $translator;
    private ClimaTempoHelper $climaTempoHelper;
    private ConfigurationRepository $configurationRepository;

    public function __construct(
            CityRepository $cityRepository,
            TranslatorInterface $translator,
            ClimaTempoHelper $climaTempoHelper,
            ConfigurationRepository $configurationRepository
    )
    {
        $this->cityRepository = $cityRepository;
        $this->translator = $translator;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->configurationRepository = $configurationRepository;
    }

    public function index(Request $request): Response
    {
        $countries = $this->cityRepository->listCountry();
        $country = null;
        $states = null;
        $state = null;
        $cityName = null;
        $cities = $this->cityRepository->listCitySelected();
        $page = 1;
        $pages = null;
        $registers = 10;
        $count = 0;

        if ('POST' === $request->getMethod()) {
            $country = $request->request->get('country');
            $states = $this->cityRepository->listStateFromCountry($country);
            $state = $request->request->get('state');
            $cityName = $request->request->get('name');

            if ($request->request->getInt('page') > 1) {
                $page = $request->request->getInt('page');
            }

            $registers = $request->request->getInt('registers');

            $firstResult = (($page - 1) * $registers) + 1;

            $cities = $this->cityRepository->listCityFromCountryAndState($country, $state, $cityName, $firstResult, $registers);

            if ($cities->count() > 0) {
                $pages = floor($cities->count() / $registers);
                
                if ($cities->count() % $registers > 0) {
                    $pages = $pages + 1;
                }
                
                $count = $cities->count();
            }
        }

        return $this->render('city/index.html.twig', [
                    'countries' => $countries,
                    'country' => $country,
                    'states' => $states,
                    'state' => $state,
                    'cityName' => $cityName,
                    'cities' => $cities,
                    'page' => $page,
                    'pages' => $pages,
                    'registers' => $registers,
                    'count' => $count,
        ]);
    }

    public function statesJson(Request $request): JsonResponse
    {
        $country = $request->request->get('country');

        $states = $this->cityRepository->listStateFromCountry($country);

        $message = (null == $states) ? 'fail' : 'success';

        return $this->json(['message' => $message, 'states' => $states]);
    }

    public function selectCity(Request $request): Response
    {
        $id = $request->get('cityId');

        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        $city = $this->cityRepository->select($id);

        $this->addFlash('success', $this->translator->trans('message.city.select.success', [
                    'city' => $city->getName(),
                    'state' => $city->getState(),
                    'country' => $city->getCountry(),
        ]));

        //$climaTempo = json_decode($this->climaTempoHelper->addCity($city, $token->getParamValue()));

        if ($this->climaTempoHelper->getError()) {
            $this->addFlash('danger', $this->climaTempoHelper->getError());
        }

        if (isset($climaTempo->error)) {
            $this->addFlash('danger', $climaTempo->detail);
        }

        if (isset($climaTempo->status)) {
            $this->addFlash('success', 'Locales: ' . implode(',', $climaTempo->locales));
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
