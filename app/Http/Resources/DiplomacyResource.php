<?php

namespace App\resources;

/**
 * @property mixed $hirtam
 * @property mixed $pvitul 
 * @property mixed $khanz
 * @property mixed $ter
 * @property mixed $fansalplains
 */
class DiplomacyResource extends Resource
{
    public function __construct($resource = null)
    {
        parent::__construct([
            "hirtam" => 0,
            "pvitul" => 0,
            "khanz" => 0,
            "ter" => 0,
            "fansalplains" => 0
        ], $resource);
    }

    public function toArray(): array
    {
        return [
            "hirtam" => $this->hirtam,
            "pvitul" => $this->pvitul,
            "khanz" => $this->khanz,
            "ter" => $this->ter,
            "fansalplains" => $this->fansalplains
        ];
    }

    public static function mapping(array $data): array
    {
        return $data;
    }
}
