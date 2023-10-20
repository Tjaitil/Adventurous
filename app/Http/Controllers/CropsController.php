<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Crop;
use App\models\Farmer;
use App\models\FarmerWorkforce;
use App\services\CountdownService;
use App\services\HungerService;
use App\services\InventoryService;
use App\services\LocationService;
use App\services\SessionService;
use App\services\SkillsService;
use Carbon\Carbon;
use Respect\Validation\Validator;

class CropsController extends controller
{
    public $data;

    function __construct(
        private InventoryService $inventoryService,
        private CountdownService $countdownService,
        private SessionService $sessionService,
        private HungerService $hungerService,
        private SkillsService $skillsService,
        private LocationService $locationService
    ) {
        parent::__construct();
    }

    public function index()
    {
        $this->data['action_items'] = Crop::all()->sortBy('farmer_level');
        $this->data['workforce_data'] = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $this->render('crops', 'Crops', $this->data, true, true, true);
    }

    /**
     *
     * @return Response
     */
    public function getViewData()
    {
        $this->data['crops'] = Crop::all();
        $this->data['workforce_data'] = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->first();
        $this->data['farmer_data'] = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        return Response::setData($this->data);
    }



    /**
     * Get countdown for one location
     *
     * @return Response
     */
    public function getCountdown()
    {

        $Farmer = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        if ($Farmer instanceof Farmer) {
            $farmerResource = [
                ...$Farmer->toArray(),
                'crop_finishes_at' => $Farmer->crop_finishes_at->timestamp,
            ];
        }

        return Response::setData($Farmer ? $farmerResource : []);
    }



    /**
     * Grow new crops
     *
     * @param Request $request
     *
     * @return Response
     */
    public function growCrops(Request $request)
    {
        // Get data and validate
        $crop_type = strtolower($request->getInput('crop_type'));
        $workforce = $request->getInput('workforce_amount');

        $request->validate([
            'crop_type' => Validator::stringVal()->notEmpty(),
            'workforce_amount' => Validator::intVal()->min(1)
        ]);

        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (!$this->locationService->isCropsLocation($location)) {
            return Response::addMessage("You are in the wrong location to grow crops")->setStatus(422);
        } else if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }

        $Farmer = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (!$Farmer instanceof Farmer) {
            return Response::addMessage("Unvalid Farmer location")->setStatus(422);
        }

        $Crop = Crop::where('crop_type', $crop_type)->first();

        // Check if crop is correct
        if (!$Crop instanceof Crop) {
            return Response::addMessage('Unvalid crop')->setStatus(422);
        } else if ($Crop->location !== $location) {
            return Response::addMessage('You are in the wrong location to grow this crop')->setStatus(422);
        }

        $FarmerWorkforce = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->first();
        if (!$FarmerWorkforce instanceof FarmerWorkforce) {
            return Response::addMessage("Something unexpected happeneded. Please try again")->setStatus(422);
        }

        if ($Farmer->crop_type) {
            return Response::addMessage("Your previous crops are not finished growing yet")->setStatus(422);
        }

        if (!$this->inventoryService->hasEnoughAmount($Crop->seed_item, $Crop->seed_required)) {
            return Response::addMessage("You don't have any seed to grow")->setStatus(422);
        }

        // Check if user has the specified workforce
        if (
            $FarmerWorkforce->avail_workforce < $workforce
        ) {
            return Response::addMessage("You don't have enough workers ready")->setStatus(422);
        }

        // Calculate new countdown
        $workforce_reduction = ($Crop->time) * ($workforce * 0.005);
        $base_reduction = $Crop->time * ($FarmerWorkforce->efficiency_level * 0.01);
        $addTime = $Crop->time - $workforce_reduction - $base_reduction;

        $new_countdown = Carbon::now()->addSeconds($addTime);

        $new_workforce_amount = $FarmerWorkforce->avail_workforce - $workforce;
        $FarmerWorkforce->$location = $workforce;
        $FarmerWorkforce->avail_workforce = $new_workforce_amount;
        $FarmerWorkforce->save();

        $Farmer->crop_finishes_at = $new_countdown;
        $Farmer->crop_type = $crop_type;
        $Farmer->save();

        $this->inventoryService->edit($Crop->seed_item, -$Crop->seed_required);
        return Response::addMessage("You have started growing $crop_type")
            ->setData([
                'avail_workforce' => $new_workforce_amount,
                'new_hunger' => $this->hungerService->getCurrentHunger(),
            ])

            ->setStatus(200);
    }



    /**
     * Harvest crops
     *
     * @param Request $request
     *
     * @return Response
     */
    public function harvestCrops(Request $request)
    {

        $is_cancelling = $request->getInput('is_cancelling');

        $request->validate([
            'is_cancelling' => Validator::boolVal()
        ]);

        $location = $this->sessionService->getCurrentLocation();
        if (!$this->locationService->isCropsLocation($location)) {
            return Response::addMessage("You are in the wrong location to grow crops")->setStatus(422);
        }

        $Farmer = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (!$Farmer instanceof Farmer) {
            return Response::addMessage("Unvalid Farmer location")->setStatus(422);
        }
        if (\is_null($Farmer->crop_type)) {
            return Response::addMessage("You don't have any crops growing")->setStatus(422);
        }
        $Crop = Crop::where('crop_type', $Farmer->crop_type)->first();

        if (!$Crop instanceof Crop) {
            return Response::addMessage("Unvalid crop")->setStatus(422);
        }

        $Workforce = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->first();

        if (!$Workforce instanceof FarmerWorkforce) {
            return Response::addMessage("Something unexpected happeneded. Please try again")->setStatus(422);
        }

        if (
            Carbon::now()->isAfter($Farmer->crop_finishes_at) &&
            $Farmer->crop_type &&
            $is_cancelling
        ) {
            return Response::addMessage("Why destroy crops that is finished")->setStatus(422);
        } else if (!Carbon::now()->isAfter($Farmer->crop_finishes_at) && !$is_cancelling) {
            return Response::addMessage("Growing of crops is not finished yet")->setStatus(422);
        }

        if (!$is_cancelling) {

            $amount = rand($Crop->min_crop_count, $Crop->max_crop_count);

            $experience = $Crop->experience + (round($Crop->experience / 100 * $amount));

            $this->inventoryService->edit($Farmer->crop_type, $amount);

            $this->skillsService->updateFarmerXP($experience)->updateSkills();
        }

        $Farmer->crop_type = null;
        $Farmer->save();

        $Workforce->avail_workforce += $Workforce->{$location};
        $Workforce->{$location} = 0;
        $Workforce->save();

        return Response::addMessage("You have harvested $amount $Farmer->crop_type")
            ->setData(['avail_workforce' => $Workforce->avail_workforce])
            ->setStatus(200);
    }



    /**
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function generateSeed(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal()->min(1),
        ]);

        $CropData = Crop::where('crop_type', $item)->first();

        if (\is_null($CropData)) {
            return Response::addMessage("Unvalid crop")->setStatus(422);
        }

        $this->inventoryService->edit($CropData->seed_item, 1 * $amount);
        $this->inventoryService->edit($item, -$amount);

        return Response::setStatus(200)->addMessage("You have generated $amount seed");
    }
}
