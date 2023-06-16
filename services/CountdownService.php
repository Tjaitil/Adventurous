<?php

namespace App\services;

use \DateTime;

class CountdownService
{
    protected Datetime $datetime;

    public function __construct()
    {
        $this->datetime = new DateTime();
    }

    public function addSeconds($seconds)
    {
        $this->datetime->modify("+{$seconds} seconds");
        return $this;
    }

    public function setDateTime(DateTime $value)
    {
        $this->datetime = $value;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function toDateFormat()
    {
        return date_format($this->datetime, "Y-m-d H:i:s");
    }

    public function toTimestamp()
    {
        return date_timestamp_get($this->datetime);
    }

    /**
     * Get timestamp for now
     *
     * @return int
     */
    public function getTimestampNow()
    {
        return date_timestamp_get(new DateTime());
    }

    public function getNow()
    {
        $this->datetime = new DateTime();
        return $this;
    }

    public function getDateTime(string|int $timestamp)
    {
        $this->datetime = new DateTime();
        $this->datetime->setTimestamp($timestamp);
        return $this;
    }

    public function isDatePassed()
    {

        return $this;
    }

    public function getMinutesLeft(string $timestamp)
    {
        $time_left =
            \date_timestamp_get(new DateTime($timestamp)) -
            \date_timestamp_get(new DateTime());

        return round($time_left / 60);
    }

    public function addToDateTime(Datetime|int|string $date, array $add)
    {

        if (!$date instanceof DateTime) {
            $date2 = new DateTime();
            $date2->setTimestamp(intval($date));
            $date = $date2;
        }

        if (isset($add['seconds'])) {
            $seconds = $add['seconds'];
            $date->modify("+{$seconds} seconds");
        }

        return $date;
    }

    public function getDateFormat(Datetime $datetime)
    {
        return date_format($datetime, "Y-m-d H:i:s");
    }

    public function getTimestamp(Datetime|string $datetime)
    {
        if (!$datetime instanceof DateTime) {
            $datetime = new DateTime($datetime);
        }

        return date_timestamp_get($datetime);
    }

    public function hasTimestampPassed(Datetime|int|string $time)
    {
        if ($time instanceof DateTime) {
            $time = date_timestamp_get($time);
        } else if ($time instanceof string) {
            $time = intval($time);
        }

        $date = date("Y-m-d H:i:s");
        $date_timestamp = date_timestamp_get(new DateTime($date));


        return ($time < $date_timestamp) ? true : false;
    }
}
