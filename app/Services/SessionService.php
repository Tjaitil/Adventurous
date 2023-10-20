<?php

namespace App\services;

use App\enums\GameMaps;
use App\models\UserData;


/**
 * @property int $miner_level
 * @property string $username
 */
class SessionService
{

    private $default = [];
    private UserData $data;

    public function __construct(private UserData $userData)
    {
        $this->default = $_SESSION;

        $this->data = $this->userData->where('username', $this->default['username'])->first();
    }

    private function fetchData()
    {
        $this->data = $this->userData_model->find();
    }

    /**
     * 
     * @return string 
     */
    public function user()
    {
        return $this->getCurrentUsername();
    }

    /**
     * 
     * @return int
     */
    public function user_id()
    {
        return $this->data->id;
    }

    /**
     * 
     * @return string 
     */
    public function getCurrentUsername()
    {
        return $this->data->username;
    }

    /**
     * 
     * @return string 
     */
    public function getCurrentMap()
    {
        if (!isset($this->data->map_location)) {
            $this->fetchData();
        }
        return $this->data->map_location;
    }

    /**
     * 
     * @return string 
     */
    public function getLocation()
    {
        return $this->data->location;
    }

    /**
     * Get current location user is in
     *
     * @return string
     */
    public function getCurrentLocation()
    {
        return GameMaps::locationMapping()[$this->data->map_location] ?? "";
    }

    /**
     * Check if player is provided profiency
     *
     * @param string $profiency
     *
     * @return bool
     */
    public function isProfiency($profiency)
    {
        return $this->data->profiency === $profiency;
    }

    public function __get($name)
    {
        return $this->default[$name];
    }

    public function __set($name, $value)
    {
        if (is_array($this->default)) {
            $this->default[$name] = $value;
        }
    }
}
