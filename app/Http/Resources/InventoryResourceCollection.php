<?php

namespace App\Http\Resources;

use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property Collection<int, Inventory> $collection
 */
class InventoryResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => $this->collection->map(fn ($model) => [
                'id' => $model->id,
                'item' => $model->item->name,
                'amount' => $model->amount,
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'max_slots' => app(InventoryService::class)->getMaxSlots($request->user()),
            ],
        ];
    }
}
