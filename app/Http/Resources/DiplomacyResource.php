<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Diplomacy
 */
class DiplomacyResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hirtam' => $this->hirtam,
            'pvitul' => $this->pvitul,
            'khanz' => $this->khanz,
            'ter' => $this->ter,
            'fansal_plains' => $this->fansal_plains,
        ];
    }
}
