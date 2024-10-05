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

use App\Entity\Configuration;
use App\Helper\ClimaTempoHelper;
use App\Message\ErrorMessage;
use App\Message\SelectedCityMessage;
use App\Repository\CityRepository;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/city', name: 'app_city_')]
class CityController extends AbstractController
{
    private CityRepository $cityRepository;
    private TranslatorInterface $translator;
    private ClimaTempoHelper $climaTempoHelper;
    private ConfigurationRepository $configurationRepository;
    private MessageBusInterface $messageBus;

    public function __construct(
        CityRepository $cityRepository,
        TranslatorInterface $translator,
        ClimaTempoHelper $climaTempoHelper,
        ConfigurationRepository $configurationRepository,
        MessageBusInterface $messageBus,
    ) {
        $this->cityRepository = $cityRepository;
        $this->translator = $translator;
        $this->climaTempoHelper = $climaTempoHelper;
        $this->configurationRepository = $configurationRepository;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, $country = null, $state = null): Response
    {
        $countries = $this->cityRepository->listCountry();
        $states = null;
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

    #[Route('/states', name: 'states', methods: ['POST'])]
    public function statesJson(Request $request): JsonResponse
    {
        $country = $request->request->get('country');

        $states = $this->cityRepository->listStateFromCountry($country);

        $message = (null == $states) ? 'fail' : 'success';

        return $this->json(['message' => $message, 'states' => $states]);
    }

    #[Route('/select/{cityId}', name: 'select', methods: ['GET'], requirements: ['cityId' => '\d+'])]
    public function selectCity(Request $request): Response
    {
        $id = $request->get('cityId');

        /** @var Configuration $token */
        $token = $this->configurationRepository->findByName(Configuration::CONFIGURATION_TOKEN);

        $climaTempo = json_decode($this->climaTempoHelper->addCity($id, $token->getParamValue()));

        if ($this->climaTempoHelper->getError()) {
            $this->addFlash('danger', $this->climaTempoHelper->getError());

            $this->messageBus->dispatch(new ErrorMessage(0, $this->climaTempoHelper->getError()));
        }

        if (isset($climaTempo->error)) {
            $this->addFlash('danger', $climaTempo->detail);

            $this->messageBus->dispatch(new ErrorMessage(0, $climaTempo->detail));
        }

        if (isset($climaTempo->status)) {
            $city = $this->cityRepository->select($id);

            $this->addFlash('success', 'Locales: '.implode(',', $climaTempo->locales));

            $this->addFlash('success', $this->translator->trans('message.city.select.success', [
                'city' => $city->getName(),
                'state' => $city->getState(),
                'country' => $city->getCountry(),
            ]));

            $this->messageBus->dispatch(new SelectedCityMessage($city));
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
