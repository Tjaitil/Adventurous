<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{farmer: WorkforceLodgeSkillResource, miner: WorkforceLodgeSkillResource} $resource
 */
class WorkforceLodgeResource extends JsonResource
{
    /**
     * @return array{farmer: WorkforceLodgeSkillResource, miner: WorkforceLodgeSkillResource}
     */
    public function toArray(Request $request): array
    {
        return [
            'farmer' => $this->resource['farmer'],
            'miner' => $this->resource['miner'],
        ];
    }
}
