<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Message;

use App\Entity\City;

final class SelectedCityMessage
{

    private City $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function getCity(): City
    {
        return $this->city;
    }
}
