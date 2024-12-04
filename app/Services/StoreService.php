<?php

namespace App\Services;

use App\Http\Builders\StoreBuilder;
use App\Http\Resources\StoreItemResource;
use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class StoreService
{
    public function __construct(
        public StoreBuilder $storeBuilder,
        protected SkillsService $skillsService,
        protected InventoryService $inventoryService
    ) {
        $this->storeBuilder = $storeBuilder::create();
    }

    /**
     * @param  array{store_items: array<int, array<string, mixed>>}  $items
     */
    public function makeStore(array $items): self
    {
        $this->storeBuilder = $this->storeBuilder::create($items);

        return $this;
    }

    /**
     * Return true on success otherwise a jsonResponse
     *
     * @param  Collection<int, Inventory>  $Inventory
     * @return JsonResponse|AdvResponse|array{'totalPrice': int, 'totalAmount': int}
     */
    public function buyItem(Collection $Inventory, string $item, int $amount, int $userId)
    {
        try {
            $store_item = $this->getStoreItem($item);

            if (! $store_item instanceof StoreItemResource || ! $this->isStoreItem($item)) {
                return $this->logNotStoreItem($item);
            }

            if (! $this->hasSkillRequirements($store_item)) {
                return (new AdvResponse([], 422))
                    ->addMessage(GameLogService::addErrorLog('You do not have the required skill level'))
                    ->toResponse(request());
            }
            foreach ($store_item->required_items as $key => $value) {
                if (! $this->inventoryService->hasEnoughAmount(
                    $Inventory,
                    $value->name,
                    $value->amount * $amount,
                )) {
                    return $this->inventoryService->logNotEnoughAmount($value->name);
                }
            }

            foreach ($store_item->required_items as $key => $value) {
                $this->inventoryService->edit($Inventory, $value->name, -$value->amount * $amount, $userId);
            }

            if (! $this->inventoryService->hasEnoughAmount($Inventory, config('adventurous.currency'), $store_item->store_value)) {
                return $this->inventoryService->logNotEnoughAmount(config('adventurous.currency'));
            } else {
                $totalPrice = $this->calculateItemCost($store_item, $amount);

                $this->inventoryService
                    ->edit(
                        $Inventory,
                        config('adventurous.currency'),
                        -$this->calculateItemCost($store_item, $amount),
                        $userId
                    );
            }

            $totalAmount = $amount * $store_item->item_multiplier;
            if ($this->storeBuilder->build()->is_inventorable === true) {
                $this->inventoryService->edit($Inventory, $store_item->name, $totalAmount, $userId);
            }

            return ['totalPrice' => $totalPrice, 'totalAmount' => $totalAmount];
        } catch (\Exception $e) {
            return (new AdvResponse([], 500))
                ->addMessage(GameLogService::addErrorLog('Something went wrong'));
        }
    }

    /**
     * @param  Collection<int, Inventory>  $Inventory
     * @return JsonResponse|array{'totalPrice': int}|AdvResponse
     */
    public function sellItem(Collection $Inventory, string $item, int $amount, int $userId)
    {
        try {
            $store_item = $this->getStoreItem($item);

            if (! $store_item instanceof StoreItemResource || ! $this->isStoreItem($item)) {
                return $this->logNotStoreItem($item);
            }

            if (! $this->hasSkillRequirements($store_item)) {
                return (new AdvResponse([], 422))
                    ->addMessage(GameLogService::addErrorLog('You do not have the required skill level'))
                    ->toResponse(request());
            }

            if (! $this->inventoryService->hasEnoughAmount($Inventory, $store_item->name, $amount)) {
                return $this->inventoryService->logNotEnoughAmount($store_item->name);
            }

            $this->inventoryService->edit($Inventory, $store_item->name, -$amount, $userId);

            $totalPrice = $this->calculateItemCost($store_item, $amount);
            $this->inventoryService->edit(
                $Inventory,
                config('adventurous.currency'),
                $totalPrice,
                $userId
            );

            return ['totalPrice' => $totalPrice];
        } catch (\Exception $e) {

            return (new AdvResponse([], 500))
                ->addMessage(GameLogService::addErrorLog('Something went wrong'));
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
        return (new AdvResponse([], 400))
            ->addMessage(GameLogService::addErrorLog(sprintf('%s is not a store item', $name)))
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
     *
     * @param  Collection<int, Inventory>  $Inventory
     */
    public function hasRequiredItems(Collection $Inventory, string $name, int $amount): bool
    {
        $result = true;
        $store_item = $this->getStoreItem($name);
        if (! $store_item instanceof StoreItemResource) {
            return $result = false;
        }

        foreach ($store_item->required_items as $key => $value) {
            if (! $this->inventoryService->hasEnoughAmount($Inventory, $value->name, $value->amount * $amount)) {
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
        return (new AdvResponse)->addMessage(GameLogService::addErrorLog('Item is not a store item'))
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
