<?php

namespace App\models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class MinerWorkforce extends Model
{
    public $timestamps = false;

    public $table = 'miner_workforce';

    /**
     * 
     * @param string $location
     *
     * @return string
     * @throws Exception
     */
    public static function getLocationTable(string $location)
    {
        if ($location === 'golbak') {
            return 'golbak_workforce';
        } else if ($location === 'snerpiir') {
            return 'snerpiir_workforce';
        } else {
            throw new Exception("Unvalid miner location");
        }
    }
}
