<?php

namespace App\services;

use App\builders\SkillActionBuilder;
use App\builders\TimeBuilder;
use App\builders\TraderAssignmentBuilder;
use App\libs\Response;
use App\models\Farmer;
use App\models\FarmerWorkforce;
use App\models\FarmerWorkforce_model;
use App\models\MineWorkforce_model;
use App\models\Trader_model;
use App\models\TraderAssignment_model;
use App\models\Warriors;
use App\resources\SkillActionResource;
use App\services\CountdownService;

/**
 * @property SkillActionResource[] $farm_resources
 * @property SkillActionResource[] $mine_resources
 * @property TraderAssignmentResource $traderassignment_resource
 * @property array $warrior_statuses
 * @property ArmyMissions[] $army_missions
 */
class ProfiencyService
{

    public $farm_resources = [];
    public $mine_resources = [];
    public $traderassignment_resource;
    public $warrior_statuses = [];

    public function __construct(
        private CountdownService $countdownService,
        private MineWorkforce_model $mineWorkforce_model,
        private FarmerWorkforce_model $farmerWorkforce_model,
        private Trader_model $trader_model,
        private TraderAssignment_model $traderAssignment_model,
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
        $this->calculateFarmerStatus();
        $this->calculateMinerStatus();
        $this->calculateTraderStatus();
        $this->calculateWarriorStatus();


        return [
            'farmer_workforce' =>  FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->get(),
            'farmer_countdowns' =>  Farmer::where('username', $this->sessionService->getCurrentUsername())->get(),
            'farmer_resources' => $this->farm_resources,
            'miner_resources' => $this->mine_resources,
            'trader_assignment' =>  $this->traderassignment_resource,
            'warrior_statuses' => $this->warrior_statuses,
        ];
    }

    private function calculateWarriorStatus()
    {
        $data = $this->warriorService->warriors_model
            ->select('*')
            ->where('username', [$this->sessionService->getCurrentUsername()])
            ->get();
        $this->warrior_statuses = [
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

    private function calculateFarmerStatus()
    {
        foreach (\CROP_LOCATIONS as $key => $value) {
            $data = [
                'countdown' => "",
                'workforce' => $this->farmerWorkforce_model->find($value)
            ];

            $resource_builder = SkillActionBuilder::create($data);

            $countdown_builder =
                TimeBuilder::create($data['countdown'])
                ->setMinutesLeft(
                    $this->countdownService->getMinutesLeft($resource_builder->build()->countdown->countdown)
                )
                ->setIsDatePassed(
                    $this->countdownService->hasTimestampPassed($resource_builder->build()->countdown->countdown)
                );

            $resource_builder
                ->setCountdown($countdown_builder->build())
                ->setskill(\FARMER_SKILL_NAME);
            $this->farm_resources[] = $resource_builder->build()->toArray();
        }
    }

    private function calculateMinerStatus()
    {
        // foreach (\MINE_LOCATIONS as $key => $value) {
        //     $data = [
        //         'countdown' => $this->mineCountdown_model->find($value),
        //         'workforce' => $this->mineWorkforce_model->find($value)
        //     ];

        //     $resource_builder = SkillActionBuilder::create(
        //         $data
        //     );

        //     $countdown_builder =
        //         TimeBuilder::create($this->mineCountdown_model->find($value))
        //         ->setMinutesLeft(
        //             $this->countdownService->getMinutesLeft($resource_builder->build()->countdown->countdown)
        //         )
        //         ->setIsDatePassed(
        //             $this->countdownService->hasTimestampPassed($resource_builder->build()->countdown->countdown)
        //         );

        //     $resource_builder
        //         ->setCountdown($countdown_builder->build())
        //         ->setskill(\MINER_SKILL_NAME);

        //     $this->mine_resources[] = $resource_builder->build()->toArray();
        // }
    }

    private function calculateTraderStatus()
    {
        $user_trader_data = $this->trader_model->find();
        $trader_assignment_data = $this->traderAssignment_model->find($user_trader_data['assignment_id']);

        $this->traderassignment_resource = TraderAssignmentBuilder::create(
            \array_merge(
                $user_trader_data,
                $trader_assignment_data
            )
        )->build()
            ->toArray();
    }
}
