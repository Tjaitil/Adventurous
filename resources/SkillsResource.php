<?php

namespace App\resources;

/**
 * @property int $adventurer_respect
 * @property int $farmer_level
 * @property int $farmer_xp
 * @property int $farmer_next_level
 * @property int $miner_level
 * @property int $miner_xp
 * @property int $miner_next_level
 * @property int $trader_level
 * @property int $trader_xp
 * @property int $trader_next_level
 * @property int $warrior_level
 * @property int $warrior_xp
 * @property int $warrior_next_level
 */
class SkillsResource extends Resource
{

    public function __construct($ressource = null)
    {
        parent::__construct([
            "adventurer_respect" => 0,
            "farmer_level" => 0,
            "farmer_xp" => 0,
            "farmer_next_level" => 0,
            "miner_level" => 0,
            "miner_xp" => 0,
            "miner_next_level" => 0,
            "trader_level" => 0,
            "trader_xp" => 0,
            "trader_next_level" => 0,
            "warrior_level" => 0,
            "warrior_xp" => 0,
            "warrior_next_level" => 0,
        ], $ressource);
    }

    public static function mapping(array $data): array
    {
        return $data;
    }

    public function toArray(): array
    {
        return [
            "adventurer_respect" => $this->adventurer_respect,
            "farmer_level" => $this->farmer_level,
            "farmer_xp" => $this->farmer_xp,
            "farmer_next_level" => $this->farmer_next_level,
            "miner_level" => $this->miner_level,
            "miner_xp" => $this->miner_xp,
            "miner_next_level" => $this->miner_next_level,
            "trader_level" => $this->trader_level,
            "trader_xp" => $this->trader_xp,
            "trader_next_level" => $this->trader_next_level,
            "warrior_level" => $this->warrior_level,
            "warrior_xp" => $this->warrior_xp,
            "warrior_next_level" => $this->warrior_next_level,
        ];
    }
}
