<?php

namespace App\resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property StoreItemResource[] $store_items
 * @property bool $infinite_amount
 * @property string $store_name
 * @property float $discount
 * @property int $discount_as_percentage
 */
class StoreResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "name" => "",
            "discount" => 1.00,
            "store_items" => [],
            "inifinite_amount" => false,
        ], $resource);
    }



    /**
     * 
     * @param array $data 
     * @return array 
     */
    public static function mapping(array $data): array
    {
        if ($data['store_items'] instanceof Collection) {
            $data['store_items'] = $data['store_items']->toArray();
        }

        $data["store_items"] = array_map(function ($item) {
            if ($item instanceof Model) {
                $item = $item->toArray();
            }
            return new StoreItemResource($item);
        }, $data["store_items"]);


        return $data;
    }



    /**
     * Convert resource to an array
     *
     * @return StoreResource
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
            'discount' => $this->discount,
            'discount_as_percentage' => 0,
            'infinite_amount' => $this->infinite_amount,
        ];
    }
}
