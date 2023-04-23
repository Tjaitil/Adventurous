<?php

namespace App\resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property StoreItemResource[] $list
 */
class StoreResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "list" => [],
        ], $resource);
    }

    public static function mapping(array $data): array
    {
        if ($data['list'] instanceof Collection) {
            $data['list'] = $data['list']->toArray();
        }

        $data["list"] = array_map(function ($item) {
            if ($item instanceof Model) {
                $item = $item->toArray();
            }
            return new StoreItemResource($item);
        }, $data["list"]);


        return $data;
    }

    /**
     * Convert resource to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $list = [];

        foreach ($this->list as $key => $item) {
            array_push($list, $item->toArray());
        }

        return $list;
    }
}
