<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, StoreItemResource> $store_items
 * @property bool $infinite_amount
 * @property string $store_name
 * @property float $store_value_modifier
 * @property float $store_value_modifier_as_percentage
 * @property bool $is_inventorable
 */
class StoreResource extends Resource
{
    /**
     * @param array{
     *  name?: string,
     *  store_value_modifier?: float,
     *  store_name?: string,
     *  store_items: array<int, array<string, mixed>>|\Illuminate\Database\Eloquent\Collection<int, covariant \Illuminate\Database\Eloquent\Model>,
     *  infinite_amount?: bool,
     *  is_inventorable?: bool,
     * } $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        parent::__construct([
            'name' => '',
            'store_value_modifier' => 1.00,
            'store_name' => '',
            'store_items' => [],
            'infinite_amount' => false,
            'is_inventorable' => true,
        ], $resource);
    }

    public static function mapping(array $data): array
    {
        if ($data['store_items'] instanceof Collection) {
            $data['store_items'] = $data['store_items']->toArray();
        }

        $data['store_items'] = array_map(function ($item) {
            if ($item instanceof Model) {
                $item = $item->toArray();
            }

            return new StoreItemResource($item);
        }, $data['store_items']);

        return $data;
    }

    /**
     * Convert resource to an array
     */
    public function toArray(): array
    {
        $store_items = [];

        foreach ($this->store_items as $key => $item) {
            array_push($store_items, $item->toArray());
        }

        return [
            'store_items' => $store_items,
            'store_name' => $this->store_name,
            'store_value_modifier' => $this->store_value_modifier,
            'store_value_modifier_as_percentage' => 0,
            'infinite_amount' => $this->infinite_amount,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toStoreItemArray(): array
    {
        $store_items = [];

        foreach ($this->store_items as $key => $item) {
            array_push($store_items, $item->toArray());
        }

        return $store_items;
    }
}
