<?php

namespace App\actions;

class MapRequiredDataAction
{
    public function handle(array $data)
    {
        $mapped_data = [];
        foreach ($data as $key => $value) {
            $index = array_search(
                $value['item_id'],
                array_column($mapped_data, 'item_id')
            );
            if ($index !== false) {
                $mapped_data[$index]['required_items'][] =
                    ["amount" => $value['amount'], "item" => $value['required_item'], ...$value];
            } else {
                $value['required_items'][] = ["amount" => $value['amount'], "item" => $value['required_item'], ...$value];
                array_push($mapped_data, $value);
            }
        }

        return $mapped_data;
    }
}
