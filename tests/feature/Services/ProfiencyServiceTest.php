<?php

namespace Tests\Feature\Services;

use App\Models\Miner;
use App\Services\ProfiencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfiencyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_miner_statuses_include_permits_per_location(): void
    {
        $GolbakMiner = Miner::where('user_id', $this->TestUser->id)
            ->where('location', 'golbak')
            ->firstOrFail();
        $GolbakMiner->update(['permits' => 12]);

        $SnerpiirMiner = Miner::where('user_id', $this->TestUser->id)
            ->where('location', 'snerpiir')
            ->firstOrFail();
        $SnerpiirMiner->update(['permits' => 3]);

        $statuses = app(ProfiencyService::class)
            ->calculateProfienciesStatuses($this->TestUser->id);

        $permits = $statuses['miners']
            ->pluck('permits', 'location')
            ->all();

        $this->assertSame(['golbak' => 12, 'snerpiir' => 3], $permits);
    }
}
