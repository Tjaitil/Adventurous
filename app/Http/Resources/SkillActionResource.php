<?php

namespace App\resources;

/**
 * @property string $location
 * @property string $type
 * @property TimeResource $countdown
 * @property WorkforceResource $workforce
 * @property int $permits
 * @property string $skill
 */
class SkillActionResource extends Resource
{

    public function __construct($ressource = null)
    {
        parent::__construct([

            "location" => "",
            "type" => "",
            "countdown" => "",
            "workforce" => "",
            "permits" => ""
        ], $ressource);
    }

    public function toArray(): array
    {
        return [
            "location" => $this->location,
            "type" => $this->type,
            "permits" => $this->permits ?? 0,
            "countdown" => $this->countdown->toArray(),
            "workforce" => $this->workforce->toArray(),
            "skill" => $this->skill,
        ];
    }

    public static function mapping(array $data): array
    {

        if (isset($data['crop_type'])) {
            $data['type'] = $data['crop_type'];
            $data['skill'] = \FARMER_SKILL_NAME;
        } else if (isset($data['mining_type'])) {
            $data['type'] = $data['mining_type'];
            $data['skill'] = \MINER_SKILL_NAME;
        }

        $data["workforce"] = new WorkforceResource($data["workforce"]);
        $data['countdown'] = new TimeResource($data['countdown']);
        return $data;
    }
}
