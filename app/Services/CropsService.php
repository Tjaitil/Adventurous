<?php

// namespace App\Services;

// use App\Models\Crops_model;
// use App\Models\CropsCountdown_model;
// use App\Models\CropWorkforce_model;
// use \Exception;
// use \datetime;

// class CropsService
// {
//     public function __construct(
//         private CropsCountdown_model $cropsCountdown_model,
//         private Crops_model $crops_model,
//         private InventoryService $inventoryService,
//         private SessionService $sessionService,
//         private CropWorkforce_model $cropWorkforce_model,
//         private SkillsService $skillsService
//     ) {
//     }

//     /**
//      * Set growing crops
//      *
//      * @param string $location Game location
//      * @param string $crop_type Crop to grow
//      * @param int $workforce Workforce provided
//      * @throws Exception
//      *
//      * @return void
//      */
//     public function set(string $location, string $crop_type, int $workforce)
//     {
//         $countdown_data = $this->cropsCountdown_model->find($location);

//         $crop_data = $this->crops_model->find($crop_type, $location);

//         $crop_workforce = $this->cropWorkforce_model->find($location);

//         // Check if crop is correct
//         if (empty($crop_data)) {
//             throw new Exception("Unvalid crop");
//         }

//         // Check if countdown is set
//         if (!$this->isCountdownSurpassed(new Datetime($countdown_data['crop_countdown']))) {
//             throw new Exception("Your previous crops are not finished growing yet");
//         }

//         // Check if user is in right location
//         $this->sessionService->isValidCropsLocation($location);

//         // Check if user has correct seeds
//         $seed_item = $crop_data['crop_type'] . '_seed';

//         if (
//             !$this->inventoryService->findItem($seed_item) ||
//             !$this->inventoryService->hasEnoughAmount($seed_item, $crop_data['seed_required'])
//         ) {
//             throw new Exception("You don't have any seed to grow");
//         }

//         // Check if user has the specified workforce
//         if (
//             $crop_workforce['avail_workforce'] < $workforce
//         ) {
//             throw new Exception("You don't have enough workers ready");
//         }

//         // Calculate new countdown
//         $workforce_reduction = ($crop_data['time']) * ($workforce * 0.005);
//         $base_reduction = $crop_data['time'] * ($crop_workforce['efficiency_level'] * 0.01);
//         $addTime = $crop_data['time'] - $workforce_reduction - $base_reduction;
//         $date = date("Y-m-d H:i:s");
//         $newDate = new DateTime($date);
//         $newDate->modify("+{$addTime} seconds");

//         // Update
//         $this->inventoryService->edit($seed_item, -$crop_data['seed_required']);
//         $this->cropsCountdown_model->update($location, $crop_type, $newDate->format("Y-m-d H:i:s"));
//         $this->cropWorkforce_model->update(
//             $location,
//             $workforce,
//             $crop_workforce['avail_workforce'] - $workforce
//         );
//     }

//     /**
//      * Finish growing crops
//      *
//      * @param string $location Game location
//      * @throws Exception
//      *
//      * @return void
//      */
//     public function finish(string $location)
//     {
//         $countdown_data = $this->cropsCountdown_model->find($location);

//         $crop_workforce = $this->cropWorkforce_model->find($location);
//         $crop_data = $this->crops_model->find($countdown_data['crop_type'], $location);

//         // Check if user is in right location
//         $this->sessionService->isValidCropsLocation($location);

//         // Check if countdown is set
//         if (!$this->isCountdownSurpassed(new Datetime($countdown_data['crop_countdown']))) {
//             throw new Exception("Your crops are not finished growing yet");
//         }

//         // Calculate data

//         $rand_min = $crop_data['min_crop_count'] * (0.3 + $crop_workforce[$location_workforce]);
//         $rand_max = $crop_data['max_crop_count'] * (0.3 + $crop_workforce[$location_workforce]);
//         $crop_amount = round(rand($rand_min, $rand_max));
//         $xp_gained = $crop_data['experience'] + (round($crop_data['experience'] / 100 * $crop_amount));
//         $new_workforce = $crop_workforce[$location_workforce];

//         $this->inventoryService->edit($crop_data['crop_type'], $crop_amount);

//         $this->cropWorkforce_model->update($location, 0, $new_workforce);

//         $this->skillsService->skillsBuilder->addFarmerXP($xp_gained);
//         $this->skillsService->updateSkills();
//     }

//     /**
//      * Generate seeds from crop
//      * @param string $crop_type
//      * 
//      * @return void
//      */
//     public function calculateSeeds(string $crop_type, int $amount)
//     {
//         $crop_data = $this->crops_model->find($crop_type, $this->sessionService->getLocation());

//         $this->isCropTypeValid($crop_data);

//         if (!$this->inventoryService->hasEnoughAmount($crop_type, $crop_data['seed_required'])) {
//             throw new Exception(sprintf("You don't have enough of %s", $crop_type));
//         }


//         $seed_amount = rand(0, 2);
//         $seed_item = $crop_type . ' seed';

//         $this->inventoryService->edit($crop_type, -$amount);
//         $this->inventoryService->edit($seed_item, $seed_amount);
//     }

//     /**
//      * Check if crop type is valid
//      *
//      * @param array $crop_data
//      * @throws Exception
//      * @return bool
//      */
//     private function isCropTypeValid(array $crop_data)
//     {
//         if (empty($crop_data)) {
//             throw new Exception("Unvalid crop");
//         }
//     }

//     /**
//      * Check if datime is passed
//      *
//      * @param datetime $datetime
//      *
//      * @return bool
//      */
//     private function isCountdownSurpassed(datetime $datetime)
//     {
//         $date = date("Y-m-d H:i:s");
//         $date_timestamp = date_timestamp_get(new DateTime($date));
//         return (date_timestamp_get($datetime) < $date_timestamp) ? true : false;
//     }
// }
