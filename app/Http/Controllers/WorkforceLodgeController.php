<?php

namespace App\Http\Controllers;

use App\Enums\SkillNames;
use App\Exceptions\JsonException;
use App\Http\Responses\AdvResponse;
use App\Models\EfficiencyUpgrade;
use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use App\Models\User;
use App\Models\UserLevels;
use App\Services\GameLogService;
use App\Services\InventoryService;
use App\Services\LevelDataService;
use App\Services\SkillsService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkforceLodgeController extends Controller
{
    public function __construct(
        public SkillsService $skillsService,
        public InventoryService $inventoryService,
        public LevelDataService $levelDataService
    ) {}

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

    /**
     * @return \App\Http\Responses\AdvResponse|\Illuminate\Http\JsonResponse
     */
    public function upgradeEfficiency(#[CurrentUser] User $User, Request $request)
    {
        $skill = $request->input('skill');

        $UserLevels = Auth::user()->userLevels;

        if ($skill === SkillNames::FARMER->value) {
            $skillLevel = $UserLevels->farmer_level;
            $Workforce = FarmerWorkforce::where('user_id', Auth::user()->id)->first();
        } elseif ($skill === SkillNames::MINER->value) {
            $skillLevel = $UserLevels->miner_level;
            $Workforce = MinerWorkforce::where('user_id', Auth::user()->id)->first();
        } else {
            Log::warning('Could not find skill', [
                'skill' => $skill,
                'user_id' => Auth::user()->id,
            ]);

            return (new AdvResponse([], 422))
                ->addMessage(GameLogService::addErrorLog('Could not find skill'));
        }

        if (! $Workforce instanceof FarmerWorkforce && ! $Workforce instanceof MinerWorkforce) {
            throw new JsonException('Could not find farmerworkforce or minerworkforce ');
        }

        if ($Workforce->efficiency_level === $this->levelDataService->getMaxEfficiencyLevel($skillLevel)) {
            return (new AdvResponse([], 422))
                ->addMessage(
                    GameLogService::addErrorLog(
                        'You need to level up your skill before upgrading efficiency more'));
        }

        $price = EfficiencyUpgrade::where('level', $Workforce->efficiency_level)->first()->price;

        if (! $this->inventoryService->hasEnoughAmount($User->inventory, config('adventurous.currency'), $price, $User->id)) {
            return $this->inventoryService->logNotEnoughAmount(config('adventurous.currency'));
        }

        $Workforce->efficiency_level += 1;
        $Workforce->save();

        return (new AdvResponse)->setData([
            'efficiency_level' => $Workforce->efficiency_level,
            'new_efficiency_price' => EfficiencyUpgrade::where('level', $Workforce->efficiency_level)->first()->price,
        ])->addMessage(GameLogService::addSuccessLog('Efficiency upgraded'))
            ->setStatus(200);
    }
}
