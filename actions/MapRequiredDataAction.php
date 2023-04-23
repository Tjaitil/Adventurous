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
                    array("amount" => $value['amount'], "item" => $value['required_item']);
            } else {
                $value['required_items'][] = array("amount" => $value['amount'], "item" => $value['required_item']);
                array_push($mapped_data, $value);
            }
        }

        return $mapped_data;
    }
}
