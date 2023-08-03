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

use App\Entity\Temperature;

class TemperatureFactory
{
    public static function build($json): Temperature
    {
        $temperature = new Temperature();

        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $json['date'], new \DateTimeZone('America/Sao_Paulo'));

        return $temperature
                        ->setDateTime($dateTime)
                        ->setCpu(0.0)
                        ->setGpu(0.0)
                        ->setTemperature($json['temperature'])
                        ->setSensation($json['sensation'])
                        ->setWindDirection($json['wind_direction'])
                        ->setWindVelocity($json['wind_velocity'])
                        ->setHumidity($json['humidity'])
                        ->setWeatherCondition($json['condition'])
                        ->setPressure($json['pressure'])
                        ->setIcon($json['icon'])
        ;
    }
}
