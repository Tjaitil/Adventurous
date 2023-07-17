<?php

namespace App\services;

use App\models\UserData;


/**
 * @property int $miner_level
 * @property string $username
 */
class SessionService
{

    private $default = [];
    private UserData $data;
    // TODO: Move this into enum
    protected $location_maps = array(
        "2.6" => "tasnobil",
        "3.5" => "golbak",
        "5.7" => "towhar",
        "7.5" => "fagna",
        "6.6" => "cruendo",
        "6.3" => "ter",
        "5.5" => "snerpiir",
        "2.9" => "pvitul",
        "4.9" => "hirtam",
        "8.2" => "khanz",
        "4.3" => "fansal-plains"
    );

    public function __construct(private UserData $userData)
    {
        $_SESSION['username'] = 'tjaitil';
        $this->default = $_SESSION;

        $this->data = $this->userData->where('username', $this->default['username'])->first();
    }

    private function fetchData()
    {
        $this->data = $this->userData_model->find();
    }

    public function getCurrentUsername()
    {
        return $this->data->username;
    }

    public function getCurrentMap()
    {
        if (!isset($this->data->map_location)) {
            $this->fetchData();
        }
        return $this->data->map_location;
    }

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
        return $this->location_maps[\strval($this->data->map_location)] ?? "";
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
