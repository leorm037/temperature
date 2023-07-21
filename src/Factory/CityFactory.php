<?php

namespace App\Factory;

use App\Entity\City;

class CityFactory
{

    public static function build($object): City
    {
        $city = new City();

        return $city->setId($object->id)
                        ->setName($object->name)
                        ->setState($object->state)
                        ->setCountry($object->country)
        ;
    }
}
