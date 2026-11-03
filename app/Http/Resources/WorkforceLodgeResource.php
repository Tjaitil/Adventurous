<?php

namespace App\Http\Resources;

use App\Models\FarmerWorkforce;
use App\Models\MinerWorkforce;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{
 *     farmer: FarmerWorkforce,
 *     miner: MinerWorkforce,
 *     max_farmers: int,
 *     max_miners: int,
 *     farmer_efficiency_price: int,
 *     miner_efficiency_price: int,
 * } $resource
 */
class WorkforceLodgeResource extends JsonResource
{
    /**
     * @return array{farmer: array<string, mixed>, miner: array<string, mixed>}
     */
    public function toArray(Request $request): array
    {
        return [
            'farmer' => $this->workforce(
                'farmer',
                $this->resource['farmer'],
                ['towhar' => $this->resource['farmer']->towhar, 'krasnur' => $this->resource['farmer']->krasnur],
                $this->resource['max_farmers'],
                $this->resource['farmer_efficiency_price'],
            ),
            'miner' => $this->workforce(
                'miner',
                $this->resource['miner'],
                ['golbak' => $this->resource['miner']->golbak, 'snerpiir' => $this->resource['miner']->snerpiir],
                $this->resource['max_miners'],
                $this->resource['miner_efficiency_price'],
            ),
        ];
    }

    /**
     * @param  array<string, int>  $locations
     * @return array<string, mixed>
     */
    private function workforce(
        string $skill,
        FarmerWorkforce|MinerWorkforce $workforce,
        array $locations,
        int $maxWorkers,
        int $efficiencyPrice,
    ): array {
        return [
            'skill' => $skill,
            'efficiency_level' => $workforce->efficiency_level ?? 1,
            'efficiency_upgrade_price' => $efficiencyPrice,
            'workforce_total' => $workforce->workforce_total,
            'avail_workforce' => $workforce->avail_workforce,
            'locations' => collect($locations)
                ->map(fn (int $amount, string $name) => ['name' => ucfirst($name), 'amount' => $amount])
                ->values()
                ->all(),
            'max_workers' => $maxWorkers,
            'can_recruit' => max(0, $maxWorkers - $workforce->workforce_total),
        ];
    }
}
