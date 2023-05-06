<?php

namespace App\Factory;

use App\Entity\Temperature;
use DateTime;

class TemperatureFactory
{

    public static function build($json): Temperature
    {
        $temperature = new Temperature();

        $dateTime = new DateTime($json['date']);

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
