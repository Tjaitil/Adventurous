<?php

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
        $data["list"] = array_map(fn ($item) => new StoreItemResource($item), $data['list']);
        return $data;
    }

    /**
     * Convert resource to an array
     *
     * @return array
     */
    public function toArray()
    {
        $list = [];

        foreach ($this->list as $key => $item) {
            array_push($list, $item->toArray());
        }

        return $list;
    }
}
