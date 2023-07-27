<?php

namespace App\controllers;

use App\enums\SkillNames;
use App\libs\Request;
use App\libs\Response;
use App\libs\controller;
use App\services\HungerService;
use App\services\SkillsService;
use App\services\SessionService;
use App\libs\TemplateFetcher;
use App\models\Trader;
use App\models\TraderAssignment;
use App\models\TraderAssignmentType;
use App\services\DiplomacyService;
use App\services\InventoryService;
use App\services\LocationService;
use Carbon\Carbon;

class TraderAssignmentController extends controller
{
    public function __construct(
        private HungerService $hungerService,
        private SkillsService $skillsService,
        private TemplateFetcher $TemplateFetcher,
        private DiplomacyService $diplomacyService,
        private SessionService $sessionService,
        private LocationService $locationService,
        private InventoryService $inventoryService
    ) {
        parent::__construct();
    }

    public function getAssignmentCountdown()
    {
        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        return Response::addData('traderAssigmentCountdown', $Trader->trading_countdown->timestamp);
    }



    /**
     * 
     * @param \App\libs\Request $request 
     * @return Response
     */
    public function newAssignment(Request $request)
    {
        $assignment_id = $request->getInput('assignment_id');

        // Check if assignment is set
        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        $TraderAssignment = TraderAssignment::where('id', $assignment_id)->first();

        if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }
        if ($Trader->assignment_id !== 0) {
            return Response::addMessage("You already have an assignment");
        }

        if (is_null($TraderAssignment)) {
            return Response::addMessage("The assignment is not valid")->setStatus(422);
        } else if ($TraderAssignment->type->required_level > $this->skillsService->userLevels->trader_level) {
            return Response::addMessage("You don't have the required level to do this assignment")->setStatus(422);
        } else if ($TraderAssignment->base !== $this->sessionService->getLocation()) {
            return Response::addMessage(WRONG_LOCATION_ERROR)->setStatus(400);
        }


        $Trader->assignment_id = $assignment_id;
        $Trader->trading_countdown = Carbon::now()->addMinutes($TraderAssignment->assignment_time);
        $Trader->cart_amount = $Trader->cart->capasity;
        $Trader->save();

        $this->skillsService->updateTraderXP($TraderAssignment->type->xp_started)->updateSkills();
        $this->hungerService->setNewHunger('skill');


        $blade = $this->bladeRender->run(
            'templates.traderAssignment_tpl',
            ['CurrentAssignment' => $TraderAssignment, 'Trader' => $Trader]
        );

        return Response::addMessage("Assignment started!")
            ->addTemplate(
                'TraderAssignment',
                $blade,
            );
    }



    /**
     * 
     * @param \App\libs\Request $request 
     * @param mixed $inventoryService 
     * @return Response
     */
    public function updateAssignment(Request $request, $inventoryService)
    {

        $is_delivering = $request->getInput('is_delivering');

        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();

        if ($Trader->assignment_id === 0) {
            return Response::addMessage("You don't have an assignment")->setStatus(422);
        }

        $TraderAssignment = TraderAssignment::where('id', $Trader->assignment_id)->first();

        if ($is_delivering) {
            // Check location
            if (
                $this->sessionService->getLocation() !==
                $TraderAssignment->destination
            ) {
                return Response::addMessage("You are in the wrong location to deliver items")->setStatus(422);
            }
            // If user doesn't have any items to deliver
            if ($Trader->cart_amount === 0) {
                return Response::addMessage("You don't have any items to deliver. 
                    Pick up some items first")->setStatus(422);
            }

            $this->skillsService->updateTraderXP(
                $TraderAssignment->type->xp_per_cargo * $Trader->cart_amounts
            );

            $Trader->delivered += $Trader->cart_amount;
            $Trader->cart_amount = 0;
            $is_assignment_finished = $Trader->delivered >= $TraderAssignment->assignment_amount;

            if ($is_assignment_finished) {
                $this->skillsService->updateTraderXP($TraderAssignment->type->xp_finished);

                if ($TraderAssignment->assignment_type === TraderAssignmentType::$FAVOR_TYPE) {
                    $this->diplomacyService->setNewDiplomacy(
                        $this->sessionService->getLocation(),
                        $TraderAssignment->type->diplomacy_percentage
                    );
                } else {
                    if ($this->sessionService->isProfiency(SkillNames::TRADER->value)) {
                        $item = $TraderAssignment->cargo;
                        $amount = $TraderAssignment->type->item_reward_amount;
                    } else {
                        $item = CURRENCY;
                        $amount = $TraderAssignment->type->currency_reward_amount;
                    }

                    $this->inventoryService->edit($item, $amount);
                    Response::addMessage(
                        sprintf("You got %d of %s for completing the trader assignment", $amount, $item)
                    );
                    $Trader->delivered = 0;
                    $Trader->assignment_id = 0;
                }

                $blade = $this->bladeRender->run(
                    'templates.traderAssignment_tpl',
                    ['CurrentAssignment' => $TraderAssignment, 'Trader' => $Trader]
                );
                Response::addTemplate(
                    'TraderAssignment',
                    $blade
                );
            }

            $this->skillsService->updateSkills();
            $return_data = [
                "isAssignmentFinished" => $is_assignment_finished,
                "delivered" => $Trader->delivered,
            ];

            Response::setData($return_data);
        } else {
            if ($Trader->cart_amount === $Trader->cart->capasity) {
                return Response::addMessage("Your cart is full. Empty before picking up")->setStatus(422);
            }

            // Check location
            if (
                $this->sessionService->getLocation() !==
                $TraderAssignment->base
            ) {
                return Response::addMessage("You are in the wrong location to pick up items")->setStatus(422);
            }

            $Trader->cart_amount = $Trader->cart->capasity;

            $return_data = [
                "cartAmount" => $Trader->cart_amount,
            ];

            Response::setData($return_data);
        }
        $Trader->save();
    }
}
