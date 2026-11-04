<?php

namespace App\Services;

use App\Enums\SkillNames;
use App\Exceptions\JsonException;
use App\Http\Resources\WorkforceLodgeResource;
use App\Http\Resources\WorkforceResource;
use App\Http\Responses\AdvResponse;
use App\Models\EfficiencyUpgrade;
use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use App\Models\User;
use App\Models\UserLevels;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkforceLodgeService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly LevelDataService $levelDataService,
    ) {}

    public function getWorkforceLodgeResource(User $user): WorkforceLodgeResource
    {
        $farmerWorkforce = FarmerWorkforce::where('user_id', $user->id)->first();
        $minerWorkforce = MinerWorkforce::where('user_id', $user->id)->first();
        $userLevels = UserLevels::where('user_id', $user->id)->first();

        if (! $farmerWorkforce instanceof FarmerWorkforce
            || ! $minerWorkforce instanceof MinerWorkforce
            || ! $userLevels instanceof UserLevels) {
            throw new JsonException('Could not find workforce or userlevels');
        }

        return new WorkforceLodgeResource([
            'farmer' => new WorkforceResource(
                $farmerWorkforce,
                $this->levelDataService->getMaxFarmers($userLevels->farmer_level),
                $this->getEfficiencyPrice($farmerWorkforce->efficiency_level),
            ),
            'miner' => new WorkforceResource(
                $minerWorkforce,
                $this->levelDataService->getMaxMiners($userLevels->miner_level),
                $this->getEfficiencyPrice($minerWorkforce->efficiency_level),
            ),
        ]);
    }

    public function upgradeEfficiency(User $user, ?string $skill): AdvResponse|JsonResponse
    {
        $userLevels = $user->userLevels;

        if ($skill === SkillNames::FARMER->value) {
            $skillLevel = $userLevels->farmer_level;
            $workforce = FarmerWorkforce::where('user_id', $user->id)->first();
        } elseif ($skill === SkillNames::MINER->value) {
            $skillLevel = $userLevels->miner_level;
            $workforce = MinerWorkforce::where('user_id', $user->id)->first();
        } else {
            Log::warning('Could not find skill', [
                'skill' => $skill,
                'user_id' => $user->id,
            ]);

            return (new AdvResponse([], 422))
                ->addMessage(GameLogService::addErrorLog('Could not find skill'));
        }

        if (! $workforce instanceof FarmerWorkforce && ! $workforce instanceof MinerWorkforce) {
            throw new JsonException('Could not find farmerworkforce or minerworkforce');
        }

        if ($workforce->efficiency_level === $this->levelDataService->getMaxEfficiencyLevel($skillLevel)) {
            return (new AdvResponse([], 422))
                ->addMessage(GameLogService::addErrorLog(
                    'You need to level up your skill before upgrading efficiency more'));
        }

        $price = $this->getEfficiencyPrice($workforce->efficiency_level);
        $currency = config('adventurous.currency');

        if (! $this->inventoryService->hasEnoughAmount($user->inventory, $currency, $price)) {
            return $this->inventoryService->logNotEnoughAmount($currency);
        }

        $this->inventoryService->edit($user->inventory, $currency, -$price, $user->id);

        $workforce->efficiency_level += 1;
        $workforce->save();

        return (new AdvResponse)->setData([
            'efficiency_level' => $workforce->efficiency_level,
            'new_efficiency_price' => $this->getEfficiencyPrice($workforce->efficiency_level),
        ])->addMessage(GameLogService::addSuccessLog('Efficiency upgraded'))
            ->setStatus(200);
    }

    private function getEfficiencyPrice(int $efficiencyLevel): int
    {
        return EfficiencyUpgrade::where('level', $efficiencyLevel)->value('price') ?? 0;
    }
}
