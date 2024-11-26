<?php

namespace App\Http\Builders;

use App\Http\Resources\StoreResource;

final class StoreBuilder
{
    private StoreResource $resource;

    /**
     * @param array{
     *  name?: string,
     *  store_value_modifier?: float,
     *  store_name?: string,
     *  store_items: array<int, array<string, mixed>>|\Illuminate\Database\Eloquent\Collection<int, covariant \Illuminate\Database\Eloquent\Model>,
     *  infinite_amount?: bool,
     *  is_inventorable?: bool,
     * } $resource
     */
    public function __construct($resource = null)
    {
        $this->resource = new StoreResource($resource);
    }

    /**
     * @param array{
     *  name?: string,
     *  store_value_modifier?: float,
     *  store_name?: string,
     *  store_items: array<int, array<string, mixed>>|\Illuminate\Database\Eloquent\Collection<int, covariant \Illuminate\Database\Eloquent\Model>,
     *  infinite_amount?: bool,
     *  is_inventorable?: bool,
     * } $resource
     */
    public static function create($resource = null): static
    {
        return new self($resource);
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
     * @return self
     */
    public function setInventorable(bool $is_inventorable)
    {
        $this->resource->is_inventorable = $is_inventorable;

        return $this;
    }

    /**
     * Set available item amount
     *
     * @param  string  $item  Item name
     * @param  int  $amount  New amount
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
     * @return void
     */
    public function setAdjustedStoreValueForItem(string $itemName, int|float $value)
    {
        $value = (int) $value;
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
        $this->resource->store_value_modifier_as_percentage = $decimal_modifier === 1.00 ? 0 : round($decimal_modifier * 100);

        foreach ($this->resource->store_items as $key => $item) {
            $item->adjusted_store_value = intval(round($item->store_value * $decimal_modifier));
            $item->adjusted_difference = intval(round($item->store_value - $item->adjusted_store_value));
        }

        return $this;
    }

    /**
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
     * Set updated list
     *
     * @param  array<int, \App\Http\Resources\StoreItemResource>  $list
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
     * @return self
     */
    public function setStoreName(string $name)
    {
        $this->resource->store_name = $name;

        return $this;
    }

    /**
     * @return StoreResource
     */
    public function build()
    {
        return $this->resource;
    }

    /**
     * @return $this
     */
    public function setResource(StoreResource $storeResource)
    {
        $this->resource = $storeResource;

        return $this;
    }
}
