<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Http\Resources\WorkforceLodgeResource;
use App\Http\Responses\AdvResponse;
use App\Models\EfficiencyUpgrade;
use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use App\Models\User;
use App\Models\UserLevels;
use App\Services\LevelDataService;
use App\Services\WorkforceLodgeService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkforceLodgeController extends Controller
{
    public function __construct(
        private readonly WorkforceLodgeService $workforceLodgeService,
        private readonly LevelDataService $levelDataService,
    ) {}

    public function getData(#[CurrentUser] User $User): WorkforceLodgeResource
    {
        return $this->workforceLodgeService->getWorkforceLodgeResource($User);
    }

    public function upgradeEfficiency(#[CurrentUser] User $User, Request $request): AdvResponse|JsonResponse
    {
        return $this->workforceLodgeService->upgradeEfficiency($User, $request->input('skill'));
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $FarmerWorkforce = FarmerWorkforce::where('user_id', Auth::user()->id)->first();
        $MinerWorkforce = MinerWorkforce::where('user_id', Auth::user()->id)->first();

        $UserLevels = UserLevels::where('user_id', Auth::user()->id)->first();

        if (! $UserLevels instanceof UserLevels) {
            throw new JsonException('Could not find userlevels');
        }

        $maxFarmerWorkers = $this->levelDataService->getMaxFarmers($UserLevels->farmer_level);
        $maxMinerWorkers = $this->levelDataService->getMaxMiners($UserLevels->miner_level);

        $farmer_efficiency_cost = EfficiencyUpgrade::where('level', $FarmerWorkforce->efficiency_level)->first()->price;
        $miner_efficiency_cost = EfficiencyUpgrade::where('level', $MinerWorkforce->efficiency_level)->first()->price;

        return view('workforcelodge')
            ->with('title', 'Workforce Lodge')
            ->with('FarmerWorkforce', $FarmerWorkforce)
            ->with('maxFarmerWorkers', $maxFarmerWorkers)
            ->with('MinerWorkforce', $MinerWorkforce)
            ->with('maxMinerWorkers', $maxMinerWorkers)
            ->with('farmer_efficiency_cost', $farmer_efficiency_cost)
            ->with('miner_efficiency_cost', $miner_efficiency_cost);
    }
}
