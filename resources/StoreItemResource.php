<?php

/**
 * @property string $name
 * @property int $amount
 * @property int $store_value
 * @property int $sell_value
 * @property StoreItemResource[] $required_items
 * @property int $item_multiplier Item amount to be multiplied when crafting. Default is 1
 * @property string 
 */
class StoreItemResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "name" => "",
            "amount" => "",
            "store_value" => "",
            "sell_value" => "",
            "required_items" => [],
            "item_multiplier" => 0,
        ], $resource);
    }

    public function toResource()
    {
        return $this;
    }

    /**
     * Convert resource to an array
     *
     * @return array
     */
    public function toArray()
    {
        $required_items = [];
        if (isset($this->required_items)) {
            foreach ($this->required_items as $key => $item) {
                array_push($required_items, $item->toArray());
            }
        }

        return [
            "name" => $this->name,
            "amount" => $this->amount,
            "store_value" => $this->store_value,
            "sell_value" => $this->sell_value,
            "required_items" => $required_items,
        ];
    }

    public static function mapping(array $data): array
    {
        if (isset($data['item'])) {
            $data['name'] = $data['item'];
        }

        if (!isset($data['item_multiplier'])) {
            $data['item_multiplier'] = 1;
        }

        if (isset($data['price'])) {
            $data['store_value'] = $data['price'];
        }

        if (isset($data['required_items'])) {
            $items = $data['required_items'];
            $data['required_items'] = [];
            foreach ($items as $key => $value) {
                array_push(
                    $data['required_items'],
                    new StoreItemResource($value)
                );
            }
        }

        if (!isset($data['item_multiplier'])) {
            $data['item_multiplier'] = 1;
        }

        return $data;
    }
}
