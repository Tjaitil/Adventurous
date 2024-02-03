<?php

namespace App\Services;

use App\Models\LevelData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LevelDataService
{
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getMaxMiners(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (! $LevelData instanceof LevelData) {
            Log::error('Could not find level data for level: '.$level, ['user_id' => Auth::user()->id]);

            return 1;
        }

        return $LevelData->max_mine_workers;
    }

    /**
     * @return int
     */
    public function getMaxFarmers(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (! $LevelData instanceof LevelData) {
            Log::error('Could not find level data for level: '.$level, ['user_id' => Auth::user()->id]);

            return 1;
        }

        return $LevelData->max_farm_workers;
    }

    /**
     * @return int
     */
    public function getMaxTraders(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (! $LevelData instanceof LevelData) {
            Log::error('Could not find level data for level: '.$level, ['user_id' => Auth::user()->id]);

            return 1;
        }

        return $LevelData->max_trade_workers;
    }

    /**
     * @return int|null
     *
     * @throws \App\Exceptions\JsonException
     */
    public function getMaxEfficiencyLevel(int $level)
    {
        $LevelData = LevelData::where('level', $level)->first();
        if (! $LevelData instanceof LevelData) {
            Log::error('Could not find level data for level: '.$level, ['user_id' => Auth::user()->id]);

            return 1;
        }

        return $LevelData->max_efficiency_level;
    }
}
