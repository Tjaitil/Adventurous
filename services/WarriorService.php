<?php

namespace App\services;

use App\actions\MergeIntoSubArrayAction;
use App\builders\WarriorBuilder;
use App\libs\Response;
use App\models\Warriors;
use App\models\Warriors_model;
use App\models\WarriorsArmory;
use App\models\WarriorsLevels_model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property WarriorBuilder[] $warriors_builder
 * @property Collection $warriors
 */
class WarriorService
{

    public $warrior_count = 0;


    public function __construct(
        public WarriorsArmory $armory_model,
        public Warriors_model $warriors_model,
        private SessionService $sessionService,
        private MergeIntoSubArrayAction $mergeIntoSubArrayAction
    ) {
    }

    /**
     * Get warriors associated with a username
     *
     * @param array|null $selected (Optional) Warrior ids to select
     *
     * @return Collection
     */
    public function getWarriors(array $selected = null)
    {
        $query = Warriors::where([
            ['username', $this->sessionService->getCurrentUsername()],
        ]);
        if ($selected !== null) {
            $query->whereIn('warrior_id', $selected);
        }
        $this->warriors = $data = $query->get();

        $this->warrior_count = $data->count();

        return $data;
    }

    /**
     * Get available warriors
     *
     * @param array|null $selected (Optional) Warrior ids to select
     *
     * @return Collection
     */
    public function getAvailableWarriors(array $selected = null)
    {
        $query = Warriors::where([
            ['username', $this->sessionService->getCurrentUsername()],
            ['fetch_report', 0],
            ['army_mission', 0],
            ['rest', 0],
        ]);
        if ($selected !== null) {
            $query->whereIn('warrior_id', $selected);
        }
        $this->warriors = $data = $query->get();
        $this->warrior_count = $data->count();

        return $data;
    }

    /**
     * Get available warriors with all relations
     *
     * @param array|null $selected (Optional) Warrior ids to select
     *
     * @return Collection
     */
    public function getAvailableWarriorsWithRelations(array $selected = null)
    {
        $query = Warriors::with('levels')->where([
            ['fetch_report', 0],
            ['army_mission', 0],
            ['rest', 0],
            ['username', $this->sessionService->getCurrentUsername()]
        ]);
        if ($selected !== false) {
            $query->whereIn('warrior_id', $selected);
        }
        $this->warriors = $data = $query->get();
        $this->warrior_count = $data->count();

        return $data;
    }

    /**
     *
     * @return Collection 
     */
    public function getWarriorDataWithRelations($all = false)
    {
        return Warriors::with('levels')->where('username', $this->sessionService->getCurrentUsername())->get();
    }

    /**
     *
     * @return Collection 
     */
    public function getWarriorData(array $warrior_ids, string $username)
    {
        return Warriors::whereIn('warrior_id', $warrior_ids)->where('username', $this->sessionService->getCurrentUsername());
    }

    /**
     * Helper function to create resources from collection
     *
     * @return void
     */
    public function createResourceFromCollection()
    {
        $resources = [];
        foreach ($this->warriors as $key => $value) {
            $resources[] = $value->toArray();
        }

        return $resources;
    }

    /**
     * Merge multiple datasets of warrior data, each index will be based on data connected to a warrior_id
     *
     * @param array ...$data
     *
     * @return void
     */
    public function mergeWarriorData(...$data)
    {
        // $this->data = [];


        // function isWarriorIDInArray(array $data_array, array $arr)
        // {
        //     return array_search(
        //         $arr['warrior_id'],
        //         array_column($data_array, "warrior_id")
        //     );
        // }
        // foreach ($data as $key => $value) {
        //     // If $value is array, then loop through array
        //     if (is_array($value)) {
        //         foreach ($value as $key => $sub_value) {
        //             $key = isWarriorIDInArray($this->data, $sub_value);

        //             if ($key === false) {
        //                 $this->data[] = $sub_value;
        //             } else {
        //                 $this->data[$key] = array_merge($this->data[$key], $sub_value);
        //             }
        //         }
        //     } else {
        //         $key = isWarriorIDInArray($this->data, $value);
        //         if ($key === false) {
        //             $this->data[] = $value;
        //         } else {
        //             $this->data[$key] = array_merge($this->data[$key], $value);
        //         }
        //     }
        // }

        // return $this->data;
    }

    public function createBuilders($data)
    {

        foreach ($data as $key => $value) {
            $this->warriors[] = WarriorBuilder::create($value);
        }
    }

    /**
     * Check if warriors is available for an action
     *
     * @param bool|array $use_resources Use resources or pass in array of warriors
     *
     * @return bool
     */
    public function isWarriorsAvailable($use_resources = true)
    {
        $is_available = true;

        if ($use_resources !== true) {
            $warriors = $use_resources;
        } else {
            $warriors = $this->warriors_builder;
        }

        foreach ($warriors as $key => $warrior) {

            if ($warrior instanceof WarriorBuilder) {
                $warrior = $warrior->build();
            }

            if (
                $warrior->army_mission !== 0 &&
                $warrior->rest !== 0 &&
                $warrior->fetch_report !== 0
            ) {
                $is_available = false;
            }
        }

        return $is_available;
    }

    /**
     * Helper functions to log warriors not available
     *
     * @return Response
     */
    public function logWarriorsNotAvailable()
    {
        return Response::addMessage("One or more of your selected warriors is not available")->setStatus(400);
    }

    /**
     *
     * @param string $location
     *
     * @return bool
     */
    public function isValidWarriorLocation(string $location)
    {
        if (!in_array($location, \WARRIOR_LOCATIONS)) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return Response
     */
    public function logInvalidWarriorLocation()
    {
        return Response::addMessage("You are in the wrong location to this action")->setStatus(400);
    }
}
