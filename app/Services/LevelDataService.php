<?php

namespace App\Services;

use App\Models\LevelData;

class LevelDataService
{
    public function __construct()
    {
    }

    /**
     * 
     * @param int $level 
     * @return int
     */
    public function getMaxMiners(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (!$LevelData instanceof LevelData) {
            return 1;
        }
        return $LevelData->max_mine_workers;
    }

    /**
     * 
     * @param int $level 
     * @return int
     */
    public function getMaxFarmers(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (!$LevelData instanceof LevelData) {
            return 1;
        }
        return $LevelData->max_farm_workers;
    }

    /**
     * 
     * @param int $level 
     * @return int
     */
    public function getMaxTraders(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (!$LevelData instanceof LevelData) {
            return 1;
        }
        return $LevelData->max_trade_workers;
    }

    /**
     * 
     * @param int $level 
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\LevelData 
     */
    public function getMaxEfficiencyLevel(int $level)
    {
        $LevelData = LevelData::where('level', 0);
        if (!$LevelData instanceof LevelData) {
            return 1;
        }

        return $LevelData->max_efficiency_level;
    }
}
