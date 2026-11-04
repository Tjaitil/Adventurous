<?php

namespace App\Http\Resources;

use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property FarmerWorkforce|MinerWorkforce $resource
 */
class WorkforceLodgeSkillResource extends JsonResource
{
    public function __construct(
        FarmerWorkforce|MinerWorkforce $resource,
        private readonly int $maxWorkers,
        private readonly int $efficiencyPrice,
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'skill' => $this->resource instanceof FarmerWorkforce ? 'farmer' : 'miner',
            'efficiency_level' => $this->resource->efficiency_level ?? 1,
            'efficiency_upgrade_price' => $this->efficiencyPrice,
            'workforce_total' => $this->resource->workforce_total,
            'avail_workforce' => $this->resource->avail_workforce,
            'max_workers' => $this->maxWorkers,
            'can_recruit' => max(0, $this->maxWorkers - $this->resource->workforce_total),
            'locations' => $this->locations(),
        ];
    }

    /**
     * @return array<int, array{name: string, amount: int}>
     */
    private function locations(): array
    {
        $amounts = $this->resource instanceof FarmerWorkforce
            ? ['Towhar' => $this->resource->towhar, 'Krasnur' => $this->resource->krasnur]
            : ['Golbak' => $this->resource->golbak, 'Snerpiir' => $this->resource->snerpiir];

        return collect($amounts)
            ->map(fn (int $amount, string $name) => ['name' => $name, 'amount' => $amount])
            ->values()
            ->all();
    }
}
