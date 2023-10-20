<?php

namespace App\Http\Builders;

use App\Http\Resources\WorkforceResource;

class WorkforceBuilder
{
    private WorkforceResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new WorkforceResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function setType(string $type)
    {
        $this->resource->type = $type;
        return $this;
    }

    public function setLocationAmount(int $amount)
    {
        $this->resource->location_amount = $amount;
        return $this;
    }

    public function setTotalAmount(int $amount)
    {

        $this->resource->total_amount = $amount;
        return $this;
    }

    public function addToTotalAmount(int $amount)
    {

        $this->resource->total_amount += $amount;
        return $this;
    }

    public function setAvailAmount(int $amount)
    {

        $this->resource->avail_amount = $amount;
        return $this;
    }

    public function addToAvailAmount(int $amount)
    {
        $this->resource->avail_amount += $amount;
        return $this;
    }

    public function incrementEfficiency()
    {
        $this->resource->efficiency_level + 1;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
