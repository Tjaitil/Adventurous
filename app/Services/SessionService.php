<?php

namespace App\Services;

use App\Enums\GameMaps;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $miner_level
 * @property string $username
 */
class SessionService
{
    private UserData $UserData;

    public function setAuthenticatedUser(User $user)
    {
        $this->UserData = UserData::where('username', $user->username)->first();
    }

    public function getUserData(): UserData
    {
        return $this->UserData = UserData::where('username', $this->getCurrentUsername())->first();
    }

    public function user(): string
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return $this->getCurrentUsername();
    }

    /**
     * @return int
     */
    public function user_id()
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return $this->UserData->id;
    }

    public function getCurrentUsername(): string
    {

        return Auth::user()->name;
    }

    public function getCurrentMap(): string
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return $this->UserData->map_location;
    }

    public function getLocation(): string
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return $this->UserData->location;
    }

    /**
     * Get current location user is in
     */
    public function getCurrentLocation(): string
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return GameMaps::locationMapping()[$this->UserData->map_location] ?? '';
    }

    /**
     * Check if player is provided profiency
     *
     * @param  string  $profiency
     */
    public function isProfiency($profiency): bool
    {
        if (! isset($this->UserData)) {
            $this->getUserData();
        }

        return $this->UserData->profiency === $profiency;
    }
}
