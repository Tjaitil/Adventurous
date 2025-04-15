<?php

namespace App\Conversation\ServerEvents;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class BaseHandler
{
    public function __construct() {}

    public function currentLocationConditional(string $location, #[CurrentUser] User $User): bool
    {
        return $User->player->location === $location;
    }
}
