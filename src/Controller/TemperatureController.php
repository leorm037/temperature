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

use App\Helper\DateTimeHelper;
use App\Repository\TemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_')]
class TemperatureController extends AbstractController
{

    public function __construct(private TemperatureRepository $temperatureRepository)
    {
        
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_temperature_days', ['days' => 1]);
    }

    #[Route('/days/{days}', name: 'temperature_days', methods: ['GET'], requirements: ['days' => '\d+'])]
    public function days(int $days = 1): Response
    {
        return $this->render('temperature/index.html.twig', compact('days'));
    }

    #[Route('/json/days', name: 'temperature_json_days', methods: ['POST'])]
    public function jsonDays(Request $request): JsonResponse
    {
        $days = intval($request->get('days', 1));

        $temperatures = $this->temperatureRepository->findByDays($days);

        return $this->json([
                    'message' => 'success',
                    'result' => $temperatures,
        ]);
    }

    #[Route('/json/detail', name: 'temperature_json_detail', methods: ['POST'])]
    public function jsonDetail(Request $request): JsonResponse
    {
        $dateString = $request->get('dateTime');

        $date = DateTimeHelper::dateTimeString($dateString);

        $temperature = $this->temperatureRepository->findByDate($date);

        $message = (null === $temperature) ? 'fail' : 'success';

        return $this->json([
                    'message' => $message,
                    'result' => $temperature,
        ]);
    }

    #[Route('/json/last-temperature', name: 'temperature_json_last_temperature', methods: ['GET'])]
    public function jsonLastTemperature(): JsonResponse
    {
        $temperature = $this->temperatureRepository->lastTemperature();

        $message = (null == $temperature) ? 'fail' : 'success';

        return $this->json([
                    'message' => $message,
                    'result' => $temperature,
        ]);
    }
}
