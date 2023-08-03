<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Factory;

use App\Entity\City;
use App\Entity\Temperature;
use App\Repository\CityRepository;
use DateTime;
use DateTimeZone;

class TemperatureFactory
{
    
    private CityRepository $cityRepository;
    
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }
    
    public function build($climaTempo): Temperature
    {
        $temperature = new Temperature();

        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $climaTempo['data']['date'], new DateTimeZone('America/Sao_Paulo'));
        
        $city = $this->cityRepository->find($climaTempo['id']);

        return $temperature
                        ->setDateTime($dateTime)
                        ->setCpu(0.0)
                        ->setGpu(0.0)
                        ->setCity($city)
                        ->setTemperature($climaTempo['data']['temperature'])
                        ->setSensation($climaTempo['data']['sensation'])
                        ->setWindDirection($climaTempo['data']['wind_direction'])
                        ->setWindVelocity($climaTempo['data']['wind_velocity'])
                        ->setHumidity($climaTempo['data']['humidity'])
                        ->setWeatherCondition($climaTempo['data']['condition'])
                        ->setPressure($climaTempo['data']['pressure'])
                        ->setIcon($climaTempo['data']['icon'])
        ;
    }
}
