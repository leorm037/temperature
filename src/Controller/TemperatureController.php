<?php

namespace App\Controller;

use App\Helper\DateTimeHelper;
use App\Repository\TemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $this->json([
                    'message' => 'success',
                    'result' => $temperatures
        ]);
    }
    
    public function jsonDetail(Request $request): JsonResponse
    {
        $dateString = $request->get('dateTime');
        
        $date = DateTimeHelper::dateTimeString($dateString, "America/Sao_Paulo");
        
        $temperature = $this->temperatureRepository->findByDate($date);
        
        $message = (null === $temperature)? 'fail' : 'success';

        return $this->json([
                    'message' => $message,
                    'result' => $temperature
        ]);
    }
    
    public function jsonLastTemperature(): JsonResponse
    {
        $temperature = $this->temperatureRepository->lastTemperature();
        
        $message = (null == $temperature) ? 'fail' : 'success';
        
        return $this->json([
            'message' => $message,
            'result' => $temperature
        ]);
    }

}
