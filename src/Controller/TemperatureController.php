<?php

namespace App\Controller;

use App\Repository\TemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function dd;

class TemperatureController extends AbstractController
{

    private TemperatureRepository $temperatureRepository;

    public function __construct(TemperatureRepository $temperatureRepository)
    {
        $this->temperatureRepository = $temperatureRepository;
    }

    public function index(): Response
    {
        return $this->redirectToRoute('app_temperature_days', ['days' => 1]);
    }

    public function days(int $days = 1): Response
    {
        return $this->render('temperature/index.html.twig', compact('days'));
    }

    public function jsonDays(Request $request): JsonResponse
    {
        $days = intval($request->get('days'));
        
        $temperatures = $this->temperatureRepository->findByDays($days);
        
        dump($temperatures);

        return $this->json([
                    'message' => 'success',
                    'result' => $temperatures
        ]);
    }

}
