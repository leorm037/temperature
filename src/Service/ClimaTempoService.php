<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use Psr\Log\LoggerInterface;

class ClimaTempoService
{

    private const URL_CITY_ADD = 'http://apiadvisor.climatempo.com.br/api-manager/user-token/:your-app-token/locales';
    private const URL_CITY_FIND = 'http://apiadvisor.climatempo.com.br/api/v1/locale/city?country=:country&token=:your-app-token';
    private const URL_CITY_WEATHER = 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/:city/current?token=:your-app-token&salt=:salt';

    private ?array $error = null;

    public function __construct(
            private LoggerInterface $logger
    )
    {
        
    }

    /**
     * @return array<string,string>
     */
    public function addCity(int $cityId, string $token)
    {
        $url = str_replace(':your-app-token', $token, self::URL_CITY_ADD);

        $handle = curl_init($url);

        $values = [
            'localeId[]' => $cityId,
        ];

        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($values));
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $request = curl_exec($handle);

        curl_close($handle);

        $this->error[] = curl_error($handle);

        if ($this->error) {
            $this->logger->error($this->error, $request);
        }

        return $request;
    }

    /**
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

        $this->error[] = curl_error($handle);

        if ($this->error) {
            $this->logger->error($this->error, $request);
        }

        if (isset($request['error']) && $request['error']) {
            $this->error[] = $request['detail'];

            return null;
        }

        return $request;
    }

    /**
     * @return array<string,string>
     */
    public function weather(int $cityId, string $token)
    {
        $url = str_replace([':city', ':your-app-token', ':salt'], [$cityId, $token, rand()], self::URL_CITY_WEATHER);

        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_HTTPGET, true);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 60000);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 60000);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $request = curl_exec($handle);

        $result = json_decode($request, true);

        curl_close($handle);

        $this->error[] = curl_error($handle);

        if ($this->error) {
            return null;
        }

        if (isset($result['error']) && $result['error']) {
            $this->error[] = $result['detail'];

            return null;
        }

        return $result;
    }

    public function getError(): ?array
    {
        return $this->error;
    }
}
