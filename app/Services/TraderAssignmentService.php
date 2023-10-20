<?php

namespace App\Services;

use \GameConstants;
use Trader_model;
use TraderAssignment_model;
use TraderAssignmentBuilder;

class TraderAssignmentService
{

    private $assignment_data = [];

    public function __construct(
        private TraderAssignment_model $traderAssignment_model,
        private Trader_model $trader_model,
        private CountdownService $countdownService,
        private SessionService $sessionService,
        public TraderAssignmentBuilder $traderAssignmentBuilder
    ) {
    }

    public function getUserAssignment()
    {
    }

    /**
     * Create TraderAssignmentBuilder
     *
     * @param int|null $id Assignment id
     *
     * @return void
     */
    public function createBuilder(int|null $id)
    {
        if (is_null($id)) {
            $user_data = $this->trader_model->find();
            $this->assignment_data = $this->traderAssignment_model->find($user_data['assignment_id']);
            $data = array_merge(
                $user_data,
                $this->assignment_data,
            );
        } else {
            $data = array_merge(
                $this->traderAssignment_model->find($id),
                $this->assignment_data = $this->trader_model->find()
            );
        }
        $this->traderAssignmentBuilder = $this->traderAssignmentBuilder::create($data);
    }

    /**
     * Update models with new data
     *
     * @return void
     */
    public function setNewAssignment()
    {
        $this->trader_model->update($this->traderAssignmentBuilder->build());
    }

    /**
     * Get countdown for new assignments
     *
     * @return int
     */
    public function getAssignmentCountdown()
    {
        return $this->traderAssignment_model->getTraderAssigmentCountdown();
    }

    /**
     * Update assignment data from builder resource. Finish assignment if assignment is finished
     *
     * @return void
     */
    public function updateAssignment()
    {

        if ($this->isAssignmentFinished()) {
            $this->trader_model->endAssignment();
        } else {
            $this->trader_model->update($this->traderAssignmentBuilder->build());
        }
    }

    public function isAssignmentFinished()
    {
        $resource = $this->traderAssignmentBuilder->build();
        if ($resource->delivered >= $resource->assignment_amount) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if trader assignment is valid
     *
     * @return bool
     */
    public function isValidAssignment()
    {

        if ($this->assignment_data === false || empty($this->assignment_data)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has an assignment
     *
     * @return bool
     */
    public function isAssignmentSet()
    {
        if ($this->traderAssignmentBuilder->build()->assignment_id > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if player is in the correct base location
     * 
     * @return bool
     */
    public function isPlayerInBaseLocation()
    {
        if ($this->sessionService->getCurrentLocation() === $this->traderAssignmentBuilder->build()->base) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user has items in trading cart
     *
     * @return bool
     */
    public function hasCartAmount()
    {
        if ($this->traderAssignmentBuilder->build()->cart_amount > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get cargo reward
     *
     * @return array $reward = [
     *     'item' => (string) Item name,
     *     'amount' => (int) Amount of item
     * ]
     */
    public function getCargoReward()
    {

        $resource = $this->traderAssignmentBuilder->build();
        //TODO: Fix reward system
        return [
            "item" => $resource->cargo,
            "amount" => 5,
        ];
    }

    /**
     * Get gold reward
     *
     * @return array $reward = [
     *     'item' => (string) GameConstants::Currency,
     *     'amount' => (int) Amount of item
     * ]
     */
    public function getNormalReward()
    {

        return [
            "item" => GameConstants::CURRENCY,
            "amount" => $this->traderAssignmentBuilder->build()->assignment_reward,
        ];
    }


    /**
     * Get experience when player delivers
     *
     * @return int
     */
    public function getDeliverExperience()
    {

        // TODO: Fix experience
        // round($assignment_type_data['per_cargo_xp'] * $row['cart_amount']);

        return round(0.2 * $this->traderAssignmentBuilder->build()->cart_amount);
    }

    /**
     * Get experience for finished assignment
     *
     * @return int
     */
    public function getExperienceReward()
    {
        return 100;
    }

    /**
     * Fill up players cart
     *
     * @return void
     */
    public function fillUpCart()
    {
        $resource = $this->traderAssignmentBuilder->build();


        $amount = $resource->cart_capasity;

        if ($resource->assignment_amount < $resource->cart_capasity + $resource->delivered) {
            $amount = $resource->assignment_amount - $resource->delivered;
        }

        $this->traderAssignmentBuilder->setCargoAmount($amount);
    }

    /**
     * Calculate assignment countdown
     *
     * @return string
     */
    public function calculateAssignmentCountdown()
    {
        $resource = $this->traderAssignmentBuilder->build();
        $date =
            $this->countdownService->getDateFormat(
                $this->countdownService->addToDateTime(
                    $this->countdownService->getTimestampNow(),
                    [
                        "seconds" => $resource->assignment_time
                    ]
                )
            );
        return $date;
    }
}
