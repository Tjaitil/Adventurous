<?php

namespace App\Conversation\Handlers;

class SailorHandler extends BaseHandler
{
    /**
     * @var array<string, string>
     */
    protected array $clientCallBacks = [
        'slrr#0' => 'GameTravelCallback',
        'slrr#1' => 'GameTravelCallback',
        'slrr#2' => 'GameTravelCallback',
        'slrr#3' => 'GameTravelCallback',
        'slrr#4' => 'GameTravelCallback',
        'slrr#5' => 'GameTravelCallback',
    ];
}
