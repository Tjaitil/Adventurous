<?php

namespace App\Services;

use App\Models\Farmer;
use App\Models\Miner;
use App\Models\Soldier;
use App\Models\Trader;
use Illuminate\Support\Collection;

class ProfiencyService
{
    public function __construct(
        private CountdownService $countdownService,
    ) {}

    /**
     * @return array{farmers: Collection<int, Farmer>|null, trader: Trader|null, miners: Collection<int, Miner>|null, warrior_statuses: array{statuses: array{finished_training: int, training: int, on_mission: int, idle: int, resting: int}}}
     */
    public function calculateProfienciesStatuses(int $userId)
    {
        return [
            'farmers' => $this->getFarmerData($userId),
            'trader' => $this->getTraderData($userId),
            'miners' => $this->getMinerData($userId),
            'warrior_statuses' => $this->calculateWarriorStatus($userId),
        ];
    }

    /**
     * @return array{statuses: array{finished_training: int, training: int, on_mission: int, idle: int, resting: int}}
     */
    public function calculateWarriorStatus(int $userId): array
    {
        $Soldier = Soldier::where('user_id', $userId)->get();

        $status = [
            'statuses' => [
                'finished_training' => 0,
                'training' => 0,
                'on_mission' => 0,
                'idle' => 0,
                'resting' => 0,
            ],
        ];

        foreach ($Soldier as $key => $value) {
            $training_countdown_passed = $this->countdownService->hasTimestampPassed($value->training_countdown);
            if ($value->is_training && $training_countdown_passed) {
                $status['statuses']['training']++;
            } elseif ($value->is_training && $training_countdown_passed) {
                $status['statuses']['training']++;
            } elseif ($value->army_mission > 0) {
                $status['statuses']['on_mission']++;
            } elseif ($value->is_resting) {
                $status['statuses']['resting']++;
            } else {
                $status['statuses']['idle']++;
            }
        }

        return $status;
    }

    /**
     * @return Collection<int, Farmer>|null
     */
    public function getFarmerData(int $userId)
    {
        // Original query (commented out for dummy data)
        $Farmer = Farmer::with(['workforce'])->where('user_id', $userId)->get();

        return $Farmer;
    }

    /**
     * @return Collection<int, Miner>|null
     */
    public function getMinerData(int $userId)
    {
        return Miner::with(['workforce'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * @return Trader|null
     */
    public function getTraderData(int $userId)
    {
        return Trader::with(['traderAssignment', 'cart'])
            ->where('user_id', $userId)
            ->first();
    }
}
