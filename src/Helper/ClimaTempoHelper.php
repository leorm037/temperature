<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Helper;

use App\Entity\City;
use Psr\Log\LoggerInterface;

class ClimaTempoHelper
{

    private const URL_CITY_ADD = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/:your-app-token/locales';
    private const URL_CITY_FIND = 'http://apiadvisor.climatempo.com.br/api/v1/locale/city?country=:country&token=:you-app-token';
    private const URL_CITY_WEATHER = 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/:city/current?token=:you-app-token&salt=:salt';

    private $error = null;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * 
     * @param City $city
     * @param string $token
     * @return array<string,string>
     */
    public function addCity(City $city, string $token)
    {
        $url = str_replace(':your-app-token', $token, self::URL_CITY_ADD);

        $handle = curl_init($url);

        $values = [
            'localeId[]' => $city->getId(),
        ];

        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($values));
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $request = curl_exec($handle);

        curl_close($handle);

        $this->error = curl_error($handle);

        if ($this->error) {
            $this->logger->error($this->error, $request);
        }

        return $request;
    }

    /**
     * 
     * @param string $country
     * @param string $token
     * @return <string,string>
     */
    public function findCities(string $country, string $token)
    {
        $url = str_replace([':country', ':your-app-token'], [$country, $token], self::URL_CITY_FIND);

        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_HTTPGET, true);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $request = curl_exec($handle);

        curl_close($handle);

        $this->error = curl_error($handle);

        if ($this->error) {
            $this->logger->error($this->error, $request);
        }

        return $request;
    }

    public function weather(int $cityId, string $token)
    {
        $url = str_replace([':city', ':your-app-token', ':CITY'], [$city, $token, rand()], self::URL_CITY_WEATHER);

        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_HTTPGET, true);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $request = curl_exec($handle);

        curl_close($handle);

        $this->error = curl_error($handle);

        if ($this->error) {
            $this->logger->error($this->error, $request);
        }

        return $request;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
