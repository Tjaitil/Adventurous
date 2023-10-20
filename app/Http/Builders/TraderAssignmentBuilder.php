<?php

namespace App\Http\Builders;

use App\Http\Resources\TraderAssignmentResource;

class TraderAssignmentBuilder
{
    private TraderAssignmentResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new TraderAssignmentResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function setAssignmentID(int $id)
    {
        $this->resource->assignment_id = $id;
        return $this;
    }

    public function setCargoAmount(int $amount)
    {
        $this->resource->cart_amount = $amount;
        return $this;
    }

    public function setDelivered(int $amount)
    {
        $this->resource->delivered = $amount;
        return $this;
    }

    public function setCountdown(string $countdown)
    {
        $this->resource->trading_countdown = $countdown;
        return $this;
    }

    public function addToDelivered(int $amount)
    {
        $this->resource->delivered += $amount;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
