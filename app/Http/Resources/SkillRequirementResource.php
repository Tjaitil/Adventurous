<?php

namespace App\Http\Resources;

use App\Enums\SkillNames;

/**
 * @property int $level
 * @property value-of<SkillNames> $skill
 */
class SkillRequirementResource extends Resource
{
    public function __construct($resource = null)
    {
        parent::__construct([
            'level' => 0,
            'skill' => '',
        ], $resource);
    }

    /**
     * Convert resource to an array
     */
    public function toArray(): array
    {

        return [
            'level' => $this->level,
            'skill' => $this->skill,
        ];
    }

    public static function mapping(array $data): array
    {
        if (isset($data['level'])) {
            $data['level'] = $data['level'];
        }

        if (isset($data['skill'])) {
            $data['skill'] = $data['skill'];
        }

        return $data;
    }
}
