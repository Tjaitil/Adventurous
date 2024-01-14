<?php

namespace App\Services;

use App\libs\Response;
use App\Models\Farmer;
use App\Models\Miner;
use App\Models\Trader;
use App\Models\Warriors;
use Illuminate\Support\Facades\Auth;

class ProfiencyService
{
    private $warrior_statuses = [];

    public function __construct(
        private CountdownService $countdownService,
        // private WarriorService $warriorService,
    ) {
    }

    /**
     * Calculate profiency statuses
     *
     * @return Response
     */
    public function calculateProfienciesStatuses()
    {
        return [
            'Farmers' => $this->getFarmerData(),
            'Trader' => $this->getTraderData(),
            'Miners' => $this->getMinerData(),
            'warrior_statuses' => $this->calculateWarriorStatus(),
        ];
    }

    /**
     * @return int[][]
     */
    private function calculateWarriorStatus()
    {
        // $data = $this->warriorService->warriors_model
        //     ->select('*')
        //     ->where('username', [$this->sessionService->getCurrentUsername()])
        //     ->get();

        return [
            'statuses' => [
                'finished_training' => 0,
                'training' => 0,
                'on_mission' => 0,
                'idle' => 0,
                'resting' => 0,
            ],
        ];

        $warriors = Warriors::where('username', Auth::user()->username);

        foreach ($warriors as $key => $value) {
            $training_countdown_passed = $this->countdownService->hasTimestampPassed($value->training_countdown);
            if ($value->fetch_report && $training_countdown_passed) {
                $this->warrior_statuses['statuses']['training']++;
            } elseif ($value->fetch_report && $training_countdown_passed) {
                $this->warrior_statuses['statuses']['training']++;
            } elseif ($value->rest) {
                $this->warrior_statuses['statuses']['resting']++;
            } else {
                $this->warrior_statuses['statuses']['idle']++;
            }
        }
    }

    /**
     * @return Collection<Farmer>|null
     */
    private function getFarmerData()
    {

        $Farmer = Farmer::with(['workforce'])->where('username', Auth::user()->username)->get();

        return $Farmer;
    }

    /**
     * @return Collection<Miner>|null
     */
    private function getMinerData()
    {
        return Miner::with(['workforce'])
            ->where('username', Auth::user()->username)
            ->get();
    }

    /**
     * @return Trader|null
     */
    private function getTraderData()
    {
        return Trader::with(['traderAssignment', 'cart'])
            ->where('username', Auth::user()->username)
            ->first();
    }
}
