<?php

namespace App\Http\Builders;

use App\Http\Resources\CountdownResource;
use App\Http\Resources\SkillActionResource;
use App\Http\Resources\TimeResource;
use App\Http\Resources\WorkforceResource;

/**
 * @deprecated 
 */
class SkillActionBuilder
{

    private SkillActionResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new SkillActionResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function setCountdown(TimeResource $resource)
    {
        $this->resource->time = $resource;
        return $this;
    }

    public function setWorkforce(WorkforceResource $resource)
    {
        $this->resource->workforce = $resource;
        return $this;
    }

    public function setPermits(int $amount)
    {
        $this->resource->permits = $amount;
        return $this;
    }

    public function incrementPermits(int $amount)
    {
        $this->resource->permits += $amount;
        return $this;
    }

    public function setType(string $type)
    {
        $this->resource->type = $type;
        return $this;
    }

    public function setActionStatus(bool $bool)
    {
        $this->resource->finished = $bool;
        return $this;
    }

    public function setskill(string $value)
    {
        $this->resource->skill = $value;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
