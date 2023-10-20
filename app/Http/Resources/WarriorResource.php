<?php

namespace App\Http\Resources;

/**
 * @property int $warrior_id
 * @property WarriorArmoryResource $armory
 * @property bool $fetch_report
 * @property string $army_mission
 * @property WarriorLevelsResource $levels
 * @property string $rest_start
 * @property string $training_countdown
 * @property string $training_type
 * @property string $type
 * @property int $health
 * @property string $location
 * @property bool $rest
 */
class WarriorResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "warrior_id" => "",
            "fetch_report" => "",
            "army_mission" => "",
            "levels" => new WarriorLevelsResource(),
            "rest_start" => "",
            "training_countdown" => "",
            "training_type" => "",
            "type" => "",
            "health" => "",
            "location" => "",
            "rest" => ""
        ], $resource);
    }

    public function toArray(): array
    {
        return [
            "training_countdown" => strtotime($this->training_countdown),
            "fetch_report" => $this->fetch_report,
            "army_mission" => $this->army_mission,
            "levels" => $this->levels->toArray(),
            "rest_start" => $this->rest_start,
            "training_type" => $this->training_type,
            "health" => $this->health,
            "location" => $this->location,
            "type" => $this->type,
            "rest" => $this->rest,
        ];
    }

    public static function mapping(array $data): array
    {
        $data['armory'] = new WarriorArmoryResource($data['armory'] ?? []);
        $data['levels'] = new WarriorLevelsResource($data['levels'] ?? []);
        $data['rest'] = (bool) $data['rest'];
        $data['fetch_report'] = (bool) $data['fetch_report'];

        return $data;
    }
}
