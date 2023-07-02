<?php

use App\libs\Request;
use App\libs\Response;
use App\libs\controller;
use App\services\HungerService;
use App\services\SkillsService;
use App\services\SessionService;
use App\libs\TemplateFetcher;
use App\services\TraderAssignmentService;

class TraderAssignmentController extends controller
{
    public function __construct(
        private TraderAssignmentService $traderAssignmentService,
        private HungerService $hungerService,
        private SkillsService $skillsService,
        private TemplateFetcher $TemplateFetcher,
        private DiplomacyService $diplomacyService,
        private SessionService $sessionService,
    ) {
        parent::__construct();
    }

    public function getAssignmentCountdown()
    {
        return Response::addData('traderAssigmentCountdown', $this->traderAssignmentService->getAssignmentCountdown());
    }

    /**
     * New assignment
     *
     * @param Request $request
     *
     * @return array
     */
    public function newAssignment(Request $request)
    {
        $assignment_id = $request->getInput('assignment_id');

        try {
            // Check if assignment is set
            $this->traderAssignmentService->createBuilder($assignment_id);

            if ($this->hungerService->isHungerTooLow()) {
                return $this->hungerService->logHungerTooLow();
            }

            if (!$this->traderAssignmentService->isValidAssignment()) {
                return Response::addMessage("The assignment is not valid")->setStatus(422);
            }

            if (!$this->traderAssignmentService->isPlayerInBaseLocation()) {
                return Response::addMessage(GameConstants::WRONG_LOCATION_ERROR)->setStatus(400);
            }


            // Throw error if user already has an assignment
            if ($this->traderAssignmentService->isAssignmentSet()) {
                return Response::addMessage("You already have an assignment");
            }

            $this->traderAssignmentService->traderAssignmentBuilder
                ->setCountdown(
                    $this->traderAssignmentService->calculateAssignmentCountdown()
                )
                ->setAssignmentID($assignment_id);

            $this->skillsService->skillsBuilder->addTraderXP(15);
            $this->hungerService->setNewHunger('skill');

            $this->traderAssignmentService->fillUpCart();
            $this->traderAssignmentService->setNewAssignment();

            return Response::addTemplate(
                'TraderAssignment',
                $this->TemplateFetcher->loadTemplate(
                    'traderAssignment',
                    $this->traderAssignmentService->traderAssignmentBuilder->build()->toArray()
                )
            );
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage() . $e->getLine() . $e->getFile())->setStatus(500);
        }
    }

    public function updateAssignment(Request $request, $inventoryService)
    {

        $is_delivering = $request->getInput('isDelivering');

        try {

            $this->traderAssignmentService->createBuilder(null);

            // Throw error if user already has an assignment
            if (!$this->traderAssignmentService->isAssignmentSet()) {
                return Response::addMessage("You don't have an assignment")->setStatus(422);
            }

            if ($is_delivering) {

                // Check location
                if (
                    $this->sessionService->getLocation() !==
                    $this->traderAssignmentService->traderAssignmentBuilder->build()->destination
                ) {
                    return Response::addMessage("You are in the wrong location to deliver items")->setStatus(422);
                }
                // If user doesn't have any items to deliver
                if (!$this->traderAssignmentService->hasCartAmount()) {
                    return Response::addMessage("You don't have any items to deliver. 
                    Pick up some items first")->setStatus(422);
                }

                $this->skillsService->skillsBuilder->addTraderXP(
                    $this->traderAssignmentService->getDeliverExperience()
                );


                $this->traderAssignmentService->traderAssignmentBuilder->addToDelivered(
                    $this->traderAssignmentService->traderAssignmentBuilder->build()->cart_amount
                )->setCargoAmount(0);

                $is_assignment_finished = $this->traderAssignmentService->isAssignmentFinished();
                if ($is_assignment_finished) {
                    $this->skillsService->skillsBuilder->addTraderXP(
                        $this->traderAssignmentService->getExperienceReward()
                    );
                    // Update diplomacy
                    if ($this->sessionService->isDiplomacyLocation()) {
                        $this->diplomacyService->setNewDiplomacy(
                            $this->sessionService->getLocation(),
                            20
                        );
                    }

                    if ($this->traderAssignmentService->traderAssignmentBuilder->build()->assignment_type !== 'favor') {

                        if ($this->sessionService->isProfiency(GameConstantS::TRADER_SKILL_NAME)) {
                            $reward = $this->traderAssignmentService->getCargoReward();
                        } else {
                            $reward = $this->traderAssignmentService->getNormalReward();
                        }

                        // Update inventory
                        $inventoryService->edit($reward['item'], $reward['amount']);
                        Response::addMessage(
                            sprintf("You got %d of %s for completing the trader assignment", $reward['amount'], $reward['item'])
                        );
                    }
                }

                $this->skillsService->updateSkills();
                $return_data = [
                    "isAssignmentFinished" => $is_assignment_finished,
                    "delivered" => $this->traderAssignmentService->traderAssignmentBuilder->build()->delivered,
                ];

                Response::setData($return_data);
            } else {
                if ($this->traderAssignmentService->hasCartAmount()) {
                    return Response::addMessage("Your cart is full. Empty before picking up")->setStatus(422);
                }

                // Check location
                if (
                    $this->sessionService->getLocation() !==
                    $this->traderAssignmentService->traderAssignmentBuilder->build()->base
                ) {
                    return Response::addMessage("You are in the wrong location to pick up items")->setStatus(422);
                }

                $this->traderAssignmentService->fillUpCart();

                $return_data = [
                    "cartAmount" => $this->traderAssignmentService->traderAssignmentBuilder->build()->cart_amount
                ];

                Response::setData($return_data);
            }

            $this->traderAssignmentService->updateAssignment();
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage())->setStatus(500);
        }
    }
}
