<?php

namespace App\Http\Resources;

use App\Models\Stockpile;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Stockpile> $collection
 */
class StockpileCollection extends ResourceCollection
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
                'max_slots' => UserData::where('id', $request->user()->id)
                    ->first()?->stockpile_max_amount,
            ],
        ];
    }
}
