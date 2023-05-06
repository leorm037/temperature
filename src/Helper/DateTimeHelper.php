<?php

namespace App\Helper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeHelper
{

    public static function currentDateTimeUTC(): DateTimeInterface
    {
        $datetime = new DateTime();
        $datetime->setTimezone(new DateTimeZone('UTC'));

        return $datetime;
    }

    public static function currentDateTimeImmutableUTC(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromMutable(self::currentDateTimeUTC());
    }

    public static function dateTimeImmutableUTC(DateTimeInterface $date): DateTimeImmutable
    {
        $date->setTimezone(new \DateTimeZone('UTC'));

        return DateTimeImmutable::createFromMutable($date);
    }

}
