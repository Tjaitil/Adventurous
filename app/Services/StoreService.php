<?php

namespace App\Services;

use App\Http\Builders\StoreBuilder;
use App\Http\Resources\StoreItemResource;
use App\Http\Responses\AdvResponse;
use App\Traits\GameLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class StoreService
{
    use GameLogger;

    public function __construct(
        public StoreBuilder $storeBuilder,
        protected SkillsService $skillsService,
        protected InventoryService $inventoryService
    ) {
        $this->storeBuilder = $storeBuilder::create();
    }

    /**
     * @param  array<mixed>  $items
     */
    public function makeStore(array $items): self
    {
        if (isset($items['list'])) {
            $items['store_items'] = $items['list'];
        }

        $this->storeBuilder = $this->storeBuilder::create($items);

        return $this;
    }

    /**
     * Return true on success otherwise a jsonResponse
     *
     * @return JsonResponse|AdvResponse|array{'totalPrice': int, 'totalAmount': int}
     */
    public function buyItem(string $item, int $amount)
    {

        try {
            $store_item = $this->getStoreItem($item);

            if (! $store_item instanceof StoreItemResource || ! $this->isStoreItem($item)) {
                return $this->logNotStoreItem($item);
            }

            if (! $this->hasSkillRequirements($store_item)) {
                return (new AdvResponse([], 422))
                    ->addErrorMessage('You do not have the required skill level')
                    ->toResponse(request());
            }
            foreach ($store_item->required_items as $key => $value) {
                if (! $this->inventoryService->hasEnoughAmount(
                    $value->name,
                    $value->amount * $amount
                )) {
                    return $this->inventoryService->logNotEnoughAmount($value->name);
                }
            }

            foreach ($store_item->required_items as $key => $value) {
                $this->inventoryService->edit($value->name, -$value->amount * $amount);
            }

            if (! $this->inventoryService->hasEnoughAmount(config('adventurous.currency'), $store_item->store_value)) {
                return $this->inventoryService->logNotEnoughAmount(config('adventurous.currency'));
            } else {
                $totalPrice = $this->calculateItemCost($store_item, $amount);

                $this->inventoryService
                    ->edit(
                        config('adventurous.currency'),
                        -$this->calculateItemCost($store_item, $amount)
                    );
            }

            $totalAmount = $amount * $store_item->item_multiplier;
            if ($this->storeBuilder->build()->is_inventorable === true) {
                $this->inventoryService->edit($store_item->name, $totalAmount);
            }

            return ['totalPrice' => $totalPrice, 'totalAmount' => $totalAmount];
        } catch (\Exception $e) {
            return (new AdvResponse([], 500))
                ->addErrorMessage('Something went wrong');
        }
    }

    /**
     * @return JsonResponse|array{'totalPrice': int}|AdvResponse
     */
    public function sellItem(string $item, int $amount)
    {
        try {
            $store_item = $this->getStoreItem($item);

            if (! $store_item instanceof StoreItemResource || ! $this->isStoreItem($item)) {
                return $this->logNotStoreItem($item);
            }

            if (! $this->hasSkillRequirements($store_item)) {
                return (new AdvResponse([], 422))
                    ->addErrorMessage('You do not have the required skill level')
                    ->toResponse(request());
            }

            if (! $this->inventoryService->hasEnoughAmount($store_item->name, $amount)) {
                return $this->inventoryService->logNotEnoughAmount($store_item->name);
            }

            $this->inventoryService->edit($store_item->name, -$amount);

            $totalPrice = $this->calculateItemCost($store_item, $amount);
            $this->inventoryService->edit(
                config('adventurous.currency'),
                $totalPrice,
            );

            return ['totalPrice' => $totalPrice];
        } catch (\Exception $e) {
            return (new AdvResponse([], 500))
                ->addErrorMessage('Something went wrong');
        }
    }

    /**
     * Get store item
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
     */
    public function logNotStoreItem(string $name): JsonResponse
    {
        return (new AdvResponse([], 400))->addErrorMessage(sprintf('%s is not a store item', $name))
            ->toResponse(request());
    }

    /**
     * Get store item
     *
     * @return StoreItemResource|null
     */
    public function getStoreItem(string $name)
    {
        $matches = [];

        foreach ($this->storeBuilder->build()->store_items as $key => $item) {
            if ($item->name === $name) {
                array_push($matches, $item->toResource());
            }
        }

        return isset($matches[0]) ? $matches[0] : null;
    }

    /**
     * Log that item is not a store item
     */
    public function hasRequiredItems(string $name, int $amount = 1): bool
    {
        $result = true;
        $store_item = $this->getStoreItem($name);
        if (! $store_item instanceof StoreItemResource) {
            return $result = false;
        }

        foreach ($store_item->required_items as $key => $value) {
            if (! $this->inventoryService->hasEnoughAmount($value->name, $value->amount * $amount)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Log that item is not a store item
     */
    public function logNotEnoughAmount(): JsonResponse
    {
        return (new AdvResponse)->addErrorMessage('Item is not a store item')
            ->setStatus(400)
            ->toResponse(request());
    }

    /**
     * Create StoreItemResource from model
     *
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
     *
     * @return int
     */
    public function calculateItemCost(StoreItemResource $storeResource, int $amount)
    {
        $item_price = ($storeResource->store_value !== $storeResource->adjusted_store_value) ? $storeResource->adjusted_store_value : $storeResource->store_value;

        $price = $amount * $item_price;

        return $price;
    }

    /**
     * Check if the store has enough amount
     *
     * @return bool
     */
    public function hasItemAmount(StoreItemResource $storeItemResource, int $amount)
    {
        if ($storeItemResource->amount < $amount) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if the user has skill requirements
     */
    public function hasSkillRequirements(StoreItemResource $storeItemResource): bool
    {
        $result = false;
        $match = false;

        if (count($storeItemResource->skill_requirements) === 0) {
            return true;
        }

        foreach ($storeItemResource->skill_requirements as $key => $value) {
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

        if ($match) {
            return true;
        } else {
            return $result;
        }
    }

    /**
     * Calculate new price of item. Will be 5 percent if value is over 1500
     *
     * @param  int|float  $price  Item price
     * @param  int  $amount  Amount that is being traded
     * @param  bool  $positive  If the change is negative or positive
     * @return float New Price
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
