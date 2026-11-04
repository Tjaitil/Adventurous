<?php

namespace App\Http\Resources;

/**
 * @property int $location_amount
 * @property int $avail_amount
 * @property int $total_amount
 * @property int $efficiency_level
 * @property string $location
 * @property string $skill
 * @property string $type
 */
class WorkforceResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "location" => "",
            "location_amount" => 0,
            "avail_amount" => 0,
            "total_amount" => 0,
            "efficiency_level" => 0,
            "skill" => "",
            "type" => ""
        ], $resource);
    }

    public static function mapping(array $data): array
    {
        $data['avail_amount'] = $data['avail_workforce'];
        $data['total_amount'] = $data['workforce_total'];

        $data['total_amount'] = $data['workforce_total'];

        if (isset($data['golbak_workforce'])) {
            $data['location_amount'] = $data['golbak_workforce'];
            $data['location'] = "golbak";
        } else if (isset($data['snerpiir_workforce'])) {
            $data['location_amount'] = $data['snerpiir_workforce'];
            $data['location'] = "snerpiir";
        } else if (isset($data['towhar_workforce'])) {
            $data['location_amount'] = $data['towhar_workforce'];
            $data['location'] = "towhar";
        } else if (isset($data['krasnur_workforce'])) {
            $data['location_amount'] = $data['krasnur_workforce'];
            $data['location'] = "krasnur";
        }
        return $data;
    }

    public function toArray(): array
    {
        return [
            "location" => $this->location,
            "location_amount" => $this->location_amount,
            "avail_amount" => $this->avail_amount,
            "total_amount" => $this->total_amount,
            "efficiency_level" => $this->efficiency_level,
            "skill" => $this->skill,
            "type" => $this->type
        ];
    }
}
