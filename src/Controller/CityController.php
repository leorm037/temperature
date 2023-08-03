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
    ) {
        $this->cityRepository = $cityRepository;
        $this->translator = $translator;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->configurationRepository = $configurationRepository;
    }

    public function index(Request $request): Response
    {
        $countries = null;
        $states = null;
        $cities = $this->cityRepository->listCitySelected();
        $cityForm = new City();

        $countries = $this->cityRepository->listCountry();

        if ('POST' === $request->getMethod()) {
            $cityForm->setName($request->request->get('name'))
                    ->setCountry($request->request->get('country'))
                    ->setState($request->request->get('state'));

            $states = $this->cityRepository->listStateFromCountry($cityForm->getCountry());
            $cities = $this->cityRepository->listCityFromCountryAndState($cityForm->getCountry(), $cityForm->getState(), $cityForm->getName());
        }

        return $this->render('city/index.html.twig', [
                    'cities' => $cities,
                    'countries' => $countries,
                    'states' => $states,
                    'cityForm' => $cityForm,
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
        $id = $request->request->get('city');

        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        if ($this->isCsrfTokenValid('select'.$id, $request->request->get('_csrf'))) {
            $city = $this->cityRepository->select($id);

            $this->addFlash('success', $this->translator->trans('message.city.select.success', [
                        'city' => $city->getName(),
                        'state' => $city->getState(),
                        'country' => $city->getCountry(),
            ]));

            $climaTempo = json_decode($this->climaTempoHelper->addCity($city, $token->getParamValue()));

            if ($this->climaTempoHelper->getError()) {
                $this->addFlash('danger', $this->climaTempoHelper->getError());
            }

            if (isset($climaTempo->error)) {
                $this->addFlash('danger', $climaTempo->detail);
            }

            if (isset($climaTempo->status)) {
                $this->addFlash('success', 'Locales: '.implode(',', $climaTempo->locales));
            }
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
