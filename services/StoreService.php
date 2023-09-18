<?php

namespace App\services;

use App\builders\StoreBuilder;
use App\libs\Response;
use App\resources\StoreItemResource;
use Illuminate\Database\Eloquent\Model;

class StoreService
{

    public function __construct(
        public StoreBuilder $storeBuilder,
        protected SkillsService $skillsService,
        protected InventoryService $inventoryService
    ) {
        $this->storeBuilder = $storeBuilder::create();
    }

    public function makeStore(array $items)
    {
        if (isset($items['list'])) {
            $items['store_items'] = $items['list'];
        }

        $this->storeBuilder = $this->storeBuilder::create($items);
        return $this;
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
        foreach ($this->storeBuilder->build()->store_items as $key => $item) {
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

        foreach ($this->storeBuilder->build()->store_items as $key => $item) {
            if ($item->name === $name) {
                array_push($matches, $item->toResource());
            }
        }

        return (object) $matches[0];
    }

    /**
     * Log that item is not a store item
     *
     * @param string $name Item name
     * @param int $amount Amount of item
     *
     * @return false;
     */
    public function hasRequiredItems(string $item, int $amount = 1)
    {
        $store_item = $this->getStoreItem($item);
        if (!$store_item instanceof StoreItemResource) {
            return false;
        }

        foreach ($store_item->required_items as $key => $value) {
            if (!$this->inventoryService->hasEnoughAmount($value->name, $value->amount * $amount)) {
                return false;
                break;
            }
        }
        return true;
    }

    /**
     * Log that item is not a store item
     *
     * @return Response
     */
    public function logNotEnoughAmount()
    {
        return Response::addMessage("Item is not a store item")->setStatus(400);
    }

    /**
     * Create StoreItemResource from model
     *
     * @param Model $item
     *
     * @return StoreItemResource
     */
    public function createStoreItemResourceFromModel(Model $item)
    {
        return new StoreItemResource($item);
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
        $item_price = ($item->store_value !== $item->adjusted_store_value) ? $item->adjusted_store_value : $item->store_value;
        $price = $amount * $item_price;

        return $price;
    }

    public function calculateAdjustedBuyValue($original_price, $sell_price, $diplomacy_price_adjust, $diplomacy_price_ratio)
    {
        $buy_price = "";
        $difference = 0;
        $class = "";
        if (!isset($diplomacy_price_ratio)) {
            return array("buy_price" => $original_price, "difference" => $difference, "class" => $class);
        } else {
            // Calculate ratio, example 0.95 diplomacy would result in (0.05 in paranthesis)
            if (round(($diplomacy_price_ratio < 1))) {
                $buy_price = $original_price * (1.0 + $diplomacy_price_adjust);
                $class = "negativeDiplomacy";
            } else {
                $buy_price = $original_price * (1.0 - $diplomacy_price_adjust);
                $class = "positiveDiplomacy";
            }
            $difference = $original_price - $buy_price;
            if ($difference > 0) $difference = "- " . $difference;
            if ($buy_price < $sell_price) $buy_price = $sell_price;
        }

        return;
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
     * Check if the user has skill requirements
     * 
     */
    public function hasSkillRequirements(string $item)
    {
        $result = false;
        $match = false;

        $item = $this->getStoreItem($item);
        if (!isset($item->skill_requirements)) return true;

        foreach ($item->skill_requirements as $key => $value) {
            $skill = $value->skill;
            $level = $value->level;

            if ($this->skillsService->hasRequiredLevel($level, $skill)) {
                $result = true;
                $match = true;
            } else {
                $match = false;
                $result = false;
            }
        }

        if ($match) return true;
        else return $result;
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
