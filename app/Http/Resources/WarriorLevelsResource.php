<?php

namespace App\Http\Resources;

/**
 * @property int $warrior_id
 * @property int $stamina_level
 * @property int $stamina_xp
 * @property int $technique_level
 * @property int $technique_xp
 * @property int $precision_level
 * @property int $precision_xp
 * @property int $strength_level
 * @property int $strength_xp
 */
class WarriorLevelsResource extends Resource
{

    public function __construct($ressource = null)
    {
        parent::__construct([
            "warrior_id" => "",
            "stamina_level" => "",
            "stamina_xp" => "",
            "stamina_next_level_xp" => "",
            "technique_level" => "",
            "technique_xp" => "",
            "technique_next_level_xp" => "",
            "precision_level" => "",
            "precision_xp" => "",
            "precision_next_level_xp" => "",
            "strength_level" => "",
            "strength_xp" => "",
            "strength_next_level_xp" => "",
        ], $ressource);
    }


    public function toArray(): array
    {
        return $this->default;
    }

    public static function mapping(array $data): array
    {
        return $data;
    }

    public static function createDataStructure(array $data)
    {
        $structured_data = [];
        $structured_data['stamina_level'] = (isset($data['stamina_level'])) ? $data['stamina_level'] : 0;
        $structured_data['stamina_xp'] = (isset($data['stamina_xp'])) ? $data['stamina_xp'] : 0;
        $structured_data['technique_level'] = (isset($data['technique_level'])) ? $data['technique_level'] : 0;
        $structured_data['precision_next_level_xp'] = (isset($data['precision_next_level_xp'])) ? $data['precision_next_level_xp'] : 0;
        $structured_data['technique_xp'] = (isset($data['technique_xp'])) ? $data['technique_xp'] : 0;
        $structured_data['precision_level'] = (isset($data['precision_level'])) ? $data['precision_level'] : 0;
        $structured_data['precision_xp'] = (isset($data['precision_xp'])) ? $data['precision_xp'] : 0;
        $structured_data['strength_level'] = (isset($data['strength_level'])) ? $data['strength_level'] : 0;
        $structured_data['strength_xp'] = (isset($data['strength_xp'])) ? $data['strength_xp'] : 0;

        return $structured_data;
    }
}
