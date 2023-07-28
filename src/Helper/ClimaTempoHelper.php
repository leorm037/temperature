<?php

namespace App\Helper;

use App\Entity\City;
use Exception;
use function dump;

class ClimaTempoHelper
{

    const URL_CITY_ADD = "http://apiadvisor.climatempo.com.br/api-manager/user-token/:your-app-token/locales";

    /*
     * curl -X PUT \
     * 'http://apiadvisor.climatempo.com.br/api-manager/user-token/:your-app-token/locales' \
     *    -H 'Content-Type: application/x-www-form-urlencoded' \
     *    -d 'localeId[]=3477'
     */

    public static function addCity(City $city, string $token)
    {
        $url = str_replace(":your-app-token", $token, self::URL_CITY_ADD);
        $request = null;

            $handle = curl_init($url);

            $values = [
                'localeId[]' => $city->getId()
            ];

            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($handle, CURLOPT_TIMEOUT_MS, 10000);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 10000);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($values));
            curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

            $request = curl_exec($handle);
            
            $error = curl_error($handle);
                       
            $result = ['request' => $request, 'error' => $error];

        return $result;
    }
}
