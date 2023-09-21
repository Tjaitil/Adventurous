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

    /**
     * 
     * @param mixed $resource 
     * @return static 
     */
    public static function create($resource = null): static
    {
        return new static($resource);
    }

    /**
     * Set infinite item amount
     *
     * @return self
     */
    public function setInfiniteAmount(bool $infinite_amount)
    {
        $this->resource->infinite_amount = $infinite_amount;
        foreach ($this->resource->store_items as $key => $item) {
            $item->amount = -1;
        }
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
        foreach ($this->resource->store_items as $key => $item) {
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
    public function setAdjustedStoreValueForItem(string $itemName, int $value)
    {
        foreach ($this->resource->store_items as $key => $item) {
            if ($item->name === $itemName) {
                $item->adjusted_store_value = $value;
                $item->adjusted_difference = $item->store_value - $value;
                break;
            }
        }
    }

    /**
     * Set new store value based on a decimal modifier
     *
     * @return self
     */
    public function setAdjustedStoreValue(float $decimal_modifier)
    {
        $this->resource->store_value_modifier = $decimal_modifier;
        $this->resource->store_value_modifier_as_percentage = $decimal_modifier === 1.00 ? 0 : $decimal_modifier * 100;

        foreach ($this->resource->store_items as $key => $item) {
            $item->adjusted_store_value =  $item->store_value * (1 - $decimal_modifier);
            $item->adjusted_difference = $item->store_value - $item->adjusted_store_value;
        }

        return $this;
    }



    /**
     * 
     * @param string $itemName 
     * @param int $price 
     * @return void 
     */
    public function setStoreBuyPriceForItem(string $itemName, int $price)
    {
        foreach ($this->resource->store_items as $key => $item) {
            if ($item->name === $itemName) {
                $item->store_buy_price = $price;
                break;
            }
        }
    }



    /**
     * 
     * @param string $item 
     * @param int $price 
     * @return $this 
     */
    public function setStoreBuyPrice(string $item, int $price)
    {
        foreach ($this->resource->store_items as $key => $item) {
            if ($item->name) {
                $item->store_buy_price = $price;
                break;
            }
        }
        return $this;
    }



    /**
     * 
     * @param string $item 
     * @param string $skill 
     * @return $this 
     */
    public function setSkillRequired(string $item, string $skill)
    {
        foreach ($this->resource->store_items as $key => $item) {
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
     * @return self
     */
    public function setList($list)
    {
        $this->resource->store_items = $list;
        return $this;
    }

    /**
     * Set store name
     *
     * @param string $name
     *
     * @return self
     */
    public function setStoreName(string $name)
    {
        $this->resource->name = $name;
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

    /**
     * 
     * @param StoreResource $storeResource 
     * @return $this 
     */
    public function setResource(StoreResource $storeResource)
    {
        $this->resource = $storeResource;
        return $this;
    }
}
