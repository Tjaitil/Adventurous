<?php

namespace App\Services;

use App\Actions\MergeIntoSubArrayAction;
use App\Enums\GameLocations;
use App\Http\Builders\WarriorBuilder;
use App\Http\Responses\AdvResponse;
use App\Models\Warriors;
use App\Models\WarriorsArmory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @property WarriorBuilder[] $warriors_builder
 * @property Collection $warriors
 */
class WarriorService
{
    public $warrior_count = 0;

    public function __construct(
        public WarriorsArmory $armory_model,
        // public Warriors_model $warriors_model,
        private MergeIntoSubArrayAction $mergeIntoSubArrayAction
    ) {}

    /**
     * Get warriors associated with a username
     *
     * @param  array|null  $selected  (Optional) Warrior ids to select
     * @return Collection
     */
    public function getWarriors(?array $selected = null)
    {
        $query = Warriors::where([
            ['username', Auth::user()->username],
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
     * @param  array|null  $selected  (Optional) Warrior ids to select
     * @return Collection
     */
    public function getAvailableWarriors(?array $selected = null)
    {
        $query = Warriors::where([
            ['username', Auth::user()->username],
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
     * @param  array|null  $selected  (Optional) Warrior ids to select
     * @return Collection
     */
    public function getAvailableWarriorsWithRelations(?array $selected = null)
    {
        $query = Warriors::with('levels')->where([
            ['fetch_report', 0],
            ['army_mission', 0],
            ['rest', 0],
            ['username', Auth::user()->username],
        ]);
        if ($selected !== false) {
            $query->whereIn('warrior_id', $selected);
        }
        $this->warriors = $data = $query->get();
        $this->warrior_count = $data->count();

        return $data;
    }

    /**
     * @return Collection
     */
    public function getWarriorDataWithRelations($all = false)
    {
        return Warriors::with('levels')->where('username', Auth::user()->username)->get();
    }

    /**
     * @return Collection
     */
    public function getWarriorData(array $warrior_ids, string $username)
    {
        return Warriors::whereIn('warrior_id', $warrior_ids)->where('username', Auth::user()->username);
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
     * @param  array  ...$data
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
     * @param  SupportCollection<int, \App\Models\Soldier>  $Soldiers
     */
    public function isSoldiersAvailable(SupportCollection $Soldiers): bool
    {
        $is_available = true;

        $Soldiers->each(function ($Soldier) use (&$is_available) {
            if (
                $Soldier->army_mission !== 0 &&
                $Soldier->is_resting === true &&
                $Soldier->is_training === true
            ) {
                $is_available = false;
            }
        });

        return $is_available;
    }

    /**
     * Helper functions to log warriors not available
     */
    public function logWarriorsNotAvailable(): JsonResponse
    {
        return response()->jsonWithGameLogs([], [GameLogService::addWarningLog('One or more of your selected warriors is not available')], 400);
    }

    /**
     * @return bool
     */
    public function isValidWarriorLocation(string $location)
    {
        if (! in_array($location, GameLocations::getWarriorLocations())) {
            return false;
        }

        return true;
    }

    public function logInvalidWarriorLocation(): AdvResponse
    {
        return advResponse([], 400)->addWarningMessage('You are in the wrong location to this action');
    }
}
