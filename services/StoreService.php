<?php

namespace App\services;

use App\builders\StoreBuilder;
use App\libs\Response;
use App\resources\StoreItemResource;

class StoreService
{

    public StoreBuilder $storeBuilder;

    public function __construct(StoreBuilder $storeBuilder)
    {
        $this->storeBuilder = $storeBuilder::create();
    }

    public function makeStore(array $items)
    {
        $this->storeBuilder = $this->storeBuilder::create($items);
    }

    /**
     * Get store item
     *
     * @param string $item Item name
     *
     * @return bool
     */
    public function isStoreItem(string $name)
    {
        $matches = [];
        foreach ($this->storeBuilder->build()->list as $key => $item) {
            if ($item->name === $name) {
                array_push($matches, $item->toResource());
            }
        }
        if (count($matches) === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Log that item is not a store item
     *
     * @param string $name Item name
     *
     * @return Response
     */
    public function logNotStoreItem(string $name)
    {
        return Response::addMessage(sprintf("%s is not a store item", $name))->setStatus(400);
    }

    /**
     * Get store item
     *
     * @param string $name Item name
     *
     * @return StoreItemResource
     */
    public function getStoreItem(string $name)
    {
        $matches = [];

        foreach ($this->storeBuilder->build()->list as $key => $item) {
            if ($item->name === $name) {
                array_push($matches, $item->toResource());
            }
        }

        return $matches[0];
    }

    /**
     *  Calculate Item cost
     *
     * @param string $item
     * @param int $amount
     *
     * @return int
     */
    public function calculateItemCost(string $item, int $amount)
    {
        $item = $this->getStoreItem($item);
        $price = $amount * $item->store_value;

        return $price;
    }

    /**
     * Check if the store has enough amount
     *
     * @param StoreItemResource|array $storeItemResource
     * @param int $amount
     * 
     * 
     * @return bool
     */
    public function hasItemAmount(StoreItemResource|array $storeItemResource, int $amount)
    {
        if (is_array($storeItemResource) || $storeItemResource->amount < $amount) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Calculate new price of item. Will be 5 percent if value is over 1500
     *
     * @param int|float $price Item price
     * @param int $amount Amount that is being traded
     * @param bool $positive If the change is negative or positive
     *
     * @return int New Price
     */
    public function calculateNewPrice(int|float $price, int $amount, bool $positive)
    {

        $percentage_change = ($price > 1500) ? 0.05 : 0.03;
        $total_percentage_change = $percentage_change * $amount;
        if ($positive) {
            $new_price = $price * (1 + $total_percentage_change);
        } else {
            $new_price = $price * (1 - $total_percentage_change);
        }

        return $new_price;
    }
}