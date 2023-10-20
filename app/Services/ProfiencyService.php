<?php

namespace App\Services;

use App\libs\Response;
use App\Models\Farmer;
use App\Models\Miner;
use App\Models\Trader;
use App\Models\Warriors;
use App\Http\Resources\SkillActionResource;
use App\Services\CountdownService;

/**
 * @property SkillActionResource[] $farm_resources
 * @property SkillActionResource[] $mine_resources
 * @property TraderAssignmentResource $traderassignment_resource
 * @property array $warrior_statuses
 * @property ArmyMissions[] $army_missions
 */
class ProfiencyService
{

    public function __construct(
        private CountdownService $countdownService,
        private WarriorService $warriorService,
        private SessionService $sessionService
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
     * 
     * @return int[][] 
     */
    private function calculateWarriorStatus()
    {
        $data = $this->warriorService->warriors_model
            ->select('*')
            ->where('username', [$this->sessionService->getCurrentUsername()])
            ->get();

        return [
            'statuses' => [
                'finished_training' => 0,
                'training' => 0,
                'on_mission' => 0,
                'idle' => 0,
                'resting' => 0,
            ]
        ];


        $warriors = Warriors::where('username', $this->sessionService->getCurrentUsername());

        foreach ($warriors as $key => $value) {
            $training_countdown_passed = $this->countdownService->hasTimestampPassed($value->training_countdown);
            if ($value->fetch_report && $training_countdown_passed) {
                $this->warrior_statuses['statuses']['training']++;
            } else if ($value->fetch_report && $training_countdown_passed) {
                $this->warrior_statuses['statuses']['training']++;
            } else if ($value->rest) {
                $this->warrior_statuses['statuses']['resting']++;
            } else {
                $this->warrior_statuses['statuses']['idle']++;
            }
        }
    }

    /**
     * 
     * @return Collection<Farmer>|null
     */
    private function getFarmerData()
    {

        $Farmer = Farmer::with(['workforce'])->where('username', $this->sessionService->user())->get();

        return $Farmer;
    }

    /**
     * 
     * @return Collection<Miner>|null
     */
    private function getMinerData()
    {
        return Miner::with(['workforce'])
            ->where('username', $this->sessionService->user())
            ->get();
    }

    /**
     * 
     * @return Trader|null 
     */
    private function getTraderData()
    {
        return Trader::with(['traderAssignment', 'cart'])
            ->where('username', $this->sessionService->user())
            ->first();
    }
}
