<?php

namespace App\Http\Resources;

use App\Services\CountdownService;

/**
 * @property string $time
 * @property bool $is_date_passed
 * @property string $minutes_left
 * @property int $timestamp
 */
class TimeResource extends Resource
{

    public function __construct($ressource = null)
    {
        parent::__construct([
            "time" => "",
        ], $ressource);
    }

    public static function mapping(array $data): array
    {
        if (isset($data['crop_countdown'])) {
            $data['time'] = $data['crop_countdown'];
        } else if (isset($data['mining_countdown'])) {
            $data['time'] = $data['mining_countdown'];
        } else if (isset($data['training_countdown'])) {
            $data['time'] = $data['training_countdown'];
        }
        $countdown = new CountdownService();
        $data['is_date_passed'] = $countdown->hasTimestampPassed($data['time']);

        return $data;
    }

    public function toArray(): array
    {
        return [
            'time' => $this->time,
            'is_date_passed' => $this->is_date_passed,
            'minutes_left' => $this->minutes_left,
            'timestamp' => date_timestamp_get(new \Datetime($this->time))
        ];
    }
}
