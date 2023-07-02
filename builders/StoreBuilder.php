<?php

namespace App\builders;

use App\resources\StoreResource;

class StoreBuilder
{
    private StoreResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new StoreResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    /**
     * Set infinite item amount
     *
     * @return void
     */
    public function setInfiniteAmount(bool $infinite_amount)
    {
        $this->resource->infinite_amount = $infinite_amount;
        return $this;
    }

    /**
     * Set available item amount
     *
     * @param string $item Item name
     * @param int $amount New amount
     *
     * @return void
     */
    public function setAmount(string $item, int $amount)
    {
        foreach ($this->resource->list as $key => $item) {
            if ($item->name) {
                $item->amount = $amount;
                break;
            }
        }
    }

    /**
     * Set new store value based on a condition
     *
     * @param string $item
     * @param int $value
     *
     * @return void
     */
    public function setAdjustedStoreValueForItem(string $item, int $value)
    {
        foreach ($this->resource->list as $key => $item) {
            if ($item->name) {
                $item->adjusted_store_value = $value;
                break;
            }
        }
    }

    /**
     * Set new store value based on a percentage modifier
     *
     * @return void
     */
    public function setAdjustedStoreValue(float $percentage_modifier)
    {
        foreach ($this->resource->list as $key => $item) {
            $item->adjusted_store_value =  $item->store_value * (1 - $percentage_modifier);
            $item->adjusted_difference = $item->store_value - $item->adjusted_store_value;
        }
    }

    public function setSkillRequired(string $item, string $skill)
    {
        foreach ($this->resource->list as $key => $item) {
            if ($item->name) {
                $item->skill_level_required = $skill;
                break;
            }
        }
        return $this;
    }


    /**
     * Set updated list
     *
     * @param StoreItemResource[] $list
     *
     * @return void
     */
    public function setList($list)
    {
        $this->resource->list = $list;
        return $this;
    }

    /**
     *
     * @return StoreResource
     */
    public function build()
    {
        return $this->resource;
    }
}
