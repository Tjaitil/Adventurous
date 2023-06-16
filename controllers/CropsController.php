<?php

namespace App\controllers;

use App\builders\SkillActionBuilder;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Crop;
use App\models\Farmer;
use App\models\FarmerWorkforce;
use App\services\CountdownService;
use App\services\HungerService;
use App\services\InventoryService;
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
        private SkillActionBuilder $skillActionBuilder,
        private SessionService $sessionService,
        private HungerService $hungerService,
        private SkillsService $skillsService
    ) {
        parent::__construct();
    }

    public function index()
    {
        $this->data['action_items'] = Crop::all();
        $this->data['workforce_data'] = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        $this->render('crops', 'Crops', $this->data, true, true);
    }

    /**
     *
     * @return Response
     */
    public function getData()
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

        $farmerResource = [
            ...$Farmer->toArray(),
            'crop_countdown' => $Farmer->crop_countdown->timestamp,
        ];

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
        $crop_type = $request->getInput('crop_type');
        $workforce = $request->getInput('workforce_amount');

        $request->validate([
            'crop_type' => Validator::stringVal()->notEmpty(),
            'workforce_amount' => Validator::intVal()->min(1)
        ]);

        $location = $this->sessionService->getCurrentLocation();

        // Check if user is in right location
        if (!$this->sessionService->isValidCropsLocation($location)) {
            return Response::addMessage("You are in the wrong location to grow crops")->setStatus(422);
        } else if ($this->hungerService->isHungerTooLow()) {
            return $this->hungerService->logHungerTooLow();
        }

        $Farmer = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        $CropData = Crop::where('crop_type', $crop_type)->first();

        $WorkforceData = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->first();

        // Check if crop is correct
        if (\is_null($CropData)) {
            return Response::addMessage('Unvalid crop')->setStatus(422);
        } else if ($CropData->location !== $location) {
            return Response::addMessage('You are in the wrong location to grow this crop')->setStatus(422);
        }

        if ($Farmer->crop_type) {
            return Response::addMessage("Your previous crops are not finished growing yet")->setStatus(422);
        }


        if (!$this->inventoryService->hasEnoughAmount($CropData->seed_item, $CropData->seed_required)) {
            return Response::addMessage("You don't have any seed to grow")->setStatus(422);
        }

        // Check if user has the specified workforce
        if (
            $WorkforceData->avail_workforce < $workforce
        ) {
            return Response::addMessage("You don't have enough workers ready")->setStatus(422);
        }

        // Calculate new countdown
        $workforce_reduction = ($CropData->time) * ($workforce * 0.005);
        $base_reduction = $CropData->time * ($WorkforceData->efficiency_level * 0.01);
        $addTime = $CropData->time - $workforce_reduction - $base_reduction;

        $new_countdown = Carbon::now()->addSeconds($addTime);

        $location_table = $location . "_workforce";

        $WorkforceData->{$location_table} = $workforce;
        $WorkforceData->avail_workforce -= $workforce;
        $WorkforceData->save();

        $Farmer->crop_countdown = $new_countdown;
        $Farmer->crop_type = $crop_type;
        $Farmer->save();

        $this->inventoryService->edit($CropData->seed_item, -$CropData->seed_required);
        return Response::addMessage("You have started growing $crop_type")->setStatus(200);
    }



    /**
     * Harvest crops
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateCrops(Request $request)
    {

        $is_cancelling = $request->getInput('is_cancelling');

        $request->validate([
            'is_cancelling' => Validator::boolVal()
        ]);

        $location = $this->sessionService->getCurrentLocation();
        if (!$this->sessionService->isValidCropsLocation($location)) {
            return Response::addMessage("You are in the wrong location to grow crops")->setStatus(422);
        }

        $Farmer = Farmer::where('username', $this->sessionService->getCurrentUsername())
            ->where('location', $location)
            ->first();

        if (in_array($Farmer->crop_type, [null, "", "none"])) {
            return Response::addMessage("You don't have any crops growing")->setStatus(422);
        }
        $Crop = Crop::where('crop_type', $Farmer->crop_type)->first();

        $Workforce = FarmerWorkforce::where('username', $this->sessionService->getCurrentUsername())->first();

        if (
            Carbon::now()->isAfter($Farmer->crop_countdown) &&
            $Farmer->crop_type &&
            $is_cancelling
        ) {
            return Response::addMessage("Why destroy crops that is finished")->setStatus(422);
        } else if (!Carbon::now()->isAfter($Farmer->crop_countdown) && !$is_cancelling) {
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

        $Workforce->avail_workforce += $Workforce->{$location . "_workforce"};
        $Workforce->{$location . "_workforce"} = 0;
        $Workforce->save();

        return Response::addMessage("You have harvested $amount $Farmer->crop_type")
            ->setData(['available_workforce' => $Workforce->avail_workforce])
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
