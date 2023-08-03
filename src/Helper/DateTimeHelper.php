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

class DateTimeHelper
{
    public static function currentDateTimeUTC(): \DateTimeInterface
    {
        $datetime = new \DateTime();
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        return $datetime;
    }

    public static function dateTimeLocale(\DateTimeInterface $dateTime, string $dateTimeZoneString): \DateTimeInterface
    {
        $dateTimeResult = \DateTime::createFromInterface($dateTime);
        $dateTimeResult->setTimezone(new \DateTimeZone($dateTimeZoneString));

        return $dateTimeResult;
    }

    public static function currentDateTimeImmutableUTC(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(self::currentDateTimeUTC());
    }

    public static function dateTimeImmutableUTC(\DateTimeInterface $date): \DateTimeImmutable
    {
        $date->setTimezone(new \DateTimeZone('UTC'));

        return \DateTimeImmutable::createFromMutable($date);
    }

    public static function dateTimeStringUTC($dateString): \DateTime
    {
        return \DateTime::createFromFormat('d/m/Y H:i:s', $dateString, new \DateTimeZone('UTC'));
    }

    public static function dateTimeString($dateString, string $dateTimeZoneString = 'UTC'): \DateTime
    {
        return \DateTime::createFromFormat('d/m/Y H:i:s', $dateString, new \DateTimeZone($dateTimeZoneString));
    }
}
