<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\EfficiencyUpgrade;

class GuideDataLoaderService
{
    /**
     * Load data based on a list of data keys
     *
     * @param  array<int, string>  $dataKeys
     * @return array<string, mixed>
     */
    public function load(array $dataKeys): array
    {
        $data = [];

        foreach ($dataKeys as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $data[$value] = $this->callLoader($value);
        }

        return $data;
    }

    /**
     * @return array<int, mixed>
     */
    private function callLoader(string $key): array
    {
        return match ($key) {
            'crops' => $this->loadCrops(),
            'workforceUpgrades' => $this->loadWorkforceUpgrades(),
            default => throw new \BadMethodCallException("No loader defined for key: {$key}"),
        };
    }

    /**
     * Load crops data
     *
     * @return array<int, mixed>
     */
    protected function loadCrops(): array
    {
        return Crop::orderBy('farmer_level')->get()->map(function (Crop $crop) {
            return [
                ...$crop->toArray(),
                'image_url' => asset("images/{$crop->crop_type}.png"),
            ];
        })->toArray();
    }

    /**
     * Load workforce upgrades data
     *
     * @return array<int, mixed>
     */
    protected function loadWorkforceUpgrades(): array
    {
        return EfficiencyUpgrade::orderBy('level')->get()->toArray();
    }
}
