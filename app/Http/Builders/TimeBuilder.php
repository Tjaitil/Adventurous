<?php

namespace App\Http\Builders;

use App\Http\Resources\TimeResource;

/**
 * @deprecated 
 */
class TimeBuilder
{
    private TimeResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new TimeResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function setDatetime(string $date)
    {
        $this->resource->time = $date;
        return $this;
    }

    public function setMinutesLeft(int $value)
    {
        $this->resource->minutes_left = $value;
        return $this;
    }

    public function setIsDatePassed(bool $value)
    {
        $this->resource->is_date_passed = $value;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
