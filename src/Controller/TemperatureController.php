<?php

namespace App\Controller;

use App\Repository\TemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $list = $this->temperatureRepository->list();
        
        return $this->render('temperature/index.html.twig', compact('list'));
    }
}
