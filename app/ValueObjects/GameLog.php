<?php

namespace App\ValueObjects;

use App\Enums\GameLogTypes;
use Carbon\Carbon;

final class GameLog implements \JsonSerializable
{
    public readonly string $message;

    public readonly GameLogTypes $type;

    public readonly Carbon $timestamp;

    public function __construct($message, GameLogTypes $type)
    {
        $this->message = $message;
        $this->type = $type;
        $this->timestamp = Carbon::now();
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type->value,
            'timestamp' => $this->timestamp->toDateTimeString(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
