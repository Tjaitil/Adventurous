<?php

namespace App\Http\Resources;

use App\Http\Resources\Resource;

/**
 * @property int $warrior_id
 * @property bool $rest
 * @property string $type
 * @property string $helm
 * @property string $ammunition
 * @property string $ammunition_amount
 * @property string $left_hand
 * @property string $body
 * @property string $right_hand
 * @property string $legs
 * @property string $boots
 * @property int $attack
 * @property int $defence
 * @property int $attack_speed
 */

class WarriorArmoryResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "warrior_id" => $this->warrior_id,
            "helm" => $this->helm,
            "ammunition" => $this->ammunition,
            "ammunition_amount" => $this->ammunition_amount,
            "left_hand" => $this->left_hand,
            "body" => $this->body,
            "right_hand" => $this->right_hand,
            "legs" => $this->legs,
            "boots" => $this->boots,
            "type" => $this->type,
            "attack" => $this->attack,
            "defence" => $this->defence,
        ], $resource);
    }

    public function toArray(): array
    {
        return [
            "warrior_id" => $this->warrior_id,
            "helm" => $this->helm,
            "ammunition" => $this->ammunition,
            "ammunition_amount" => $this->ammunition_amount,
            "left_hand" => $this->left_hand,
            "body" => $this->body,
            "right_hand" => $this->right_hand,
            "legs" => $this->legs,
            "boots" => $this->boots,
            "type" => $this->type,
            "attack" => $this->attack,
            "defence" => $this->defence,
        ];
    }


    public static function mapping(array $data): array
    {

        if (isset($data['warrior']['type'])) {
            $data['type'] = $data['warrior']['type'];
        }

        return $data;
    }
}
