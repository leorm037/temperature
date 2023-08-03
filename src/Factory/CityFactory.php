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
