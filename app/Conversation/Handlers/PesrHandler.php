<?php

namespace App\Conversation\Handlers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

final class PesrHandler extends BaseHandler
{
    /**
     * @var array<string, string>
     */
    protected array $clientCallBacks = [
        'prrr#0' => 'GameTravelCallback',
        'prrr#1' => 'GameTravelCallback',
        'prrr#2' => 'GameTravelCallback',
        'prrr#3' => 'GameTravelCallback',
        'prrr#4' => 'GameTravelCallback',
        'prrr#5' => 'GameTravelCallback',
        'prrr#6' => 'GameTravelCallback',
        'prrr#7' => 'GameTravelCallback',
        'prrr#8' => 'GameTravelCallback',
    ];

    protected array $conditionals = [
        'prrr#0' => 'GameTravelConditional',
        'prrr#1' => 'GameTravelConditional',
        'prrr#2' => 'GameTravelConditional',
        'prrr#3' => 'GameTravelConditional',
        'prrr#4' => 'GameTravelConditional',
        'prrr#5' => 'GameTravelConditional',
        'prrr#6' => 'GameTravelConditional',
        'prrr#7' => 'GameTravelConditional',
        'prrr#8' => 'GameTravelConditional',
    ];

    public function GameTravelConditional(string $location, #[CurrentUser] User $User): bool
    {
        return ! $this->currentLocationConditional($location, $User);
    }
}
