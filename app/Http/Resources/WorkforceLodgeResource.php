<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{farmer: WorkforceResource, miner: WorkforceResource} $resource
 */
class WorkforceLodgeResource extends JsonResource
{
    /**
     * @return array{farmer: WorkforceResource, miner: WorkforceResource}
     */
    public function toArray(Request $request): array
    {
        return [
            'farmer' => $this->resource['farmer'],
            'miner' => $this->resource['miner'],
        ];
    }
}
