<?php

namespace Tests\Feature\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\UserData;
use App\Services\DaqloonFightingAreaService;
use App\Services\LootService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DaqloonFightingControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['adventurous.loot.claims_per_minute' => 3]);
        $this->actingAs($this->RandomUser);
        app(DaqloonFightingAreaService::class)->track($this->RandomUser->id, true);
    }

    private function getLootAmount(): int
    {
        return (int) Inventory::where('user_id', $this->RandomUser->id)
            ->whereIn('item_id', Item::whereIn('name', LootService::DROPS)->pluck('item_id'))
            ->sum('amount');
    }

    public function test_roll_loot_returns_a_daqloon_drop(): void
    {
        $response = $this->post('/fighting/progress')->assertOk();

        $this->assertContains($response->json('data.item'), LootService::DROPS);
    }

    public function test_roll_loot_outside_fighting_area_returns_no_item(): void
    {
        app(DaqloonFightingAreaService::class)->track($this->RandomUser->id, false);

        $response = $this->post('/fighting/progress')->assertOk();

        $this->assertNull($response->json('data.item'));
    }

    public function test_claim_loot_adds_rolled_item_to_inventory(): void
    {
        $item = $this->post('/fighting/progress')->json('data.item');
        $before = $this->getLootAmount();

        $this->post('/fighting/sync')->assertOk();

        $this->assertSame($before + 1, $this->getLootAmount());
        $this->assertSame(1, (int) Inventory::where('user_id', $this->RandomUser->id)
            ->where('item_id', Item::where('name', $item)->value('item_id'))
            ->value('amount'));
    }

    public function test_claim_loot_consumes_the_pending_drop(): void
    {
        $this->post('/fighting/progress');
        $this->post('/fighting/sync');
        $before = $this->getLootAmount();

        $this->post('/fighting/sync')->assertOk();

        $this->assertSame($before, $this->getLootAmount());
    }

    public function test_claim_loot_without_roll_grants_nothing(): void
    {
        $before = $this->getLootAmount();

        $this->post('/fighting/sync')->assertOk();

        $this->assertSame($before, $this->getLootAmount());
    }

    public function test_claim_loot_ignores_item_sent_by_client(): void
    {
        $before = $this->getLootAmount();

        $this->post('/fighting/sync', ['item' => config('adventurous.currency')])->assertOk();

        $this->assertSame($before, $this->getLootAmount());
    }

    public function test_claim_loot_outside_fighting_area_grants_nothing(): void
    {
        $this->post('/fighting/progress');
        app(DaqloonFightingAreaService::class)->track($this->RandomUser->id, false);
        $before = $this->getLootAmount();

        $this->post('/fighting/sync')->assertOk();

        $this->assertSame($before, $this->getLootAmount());
    }

    public function test_claim_loot_requires_authentication(): void
    {
        $this->app['auth']->forgetGuards();

        $this->postJson('/fighting/sync')->assertUnauthorized();
    }

    public function test_rate_limited_roll_looks_successful_but_queues_nothing(): void
    {
        $limit = config('adventurous.loot.claims_per_minute');
        for ($i = 0; $i < $limit; $i++) {
            $this->post('/fighting/progress')->assertOk();
        }
        $limited = $this->post('/fighting/progress');
        $before = $this->getLootAmount();

        for ($i = 0; $i < $limit + 2; $i++) {
            $this->post('/fighting/sync')->assertOk();
        }

        $limited->assertOk();
        $this->assertNull($limited->json('data.item'));
        $this->assertSame($before + $limit, $this->getLootAmount());
    }

    #[DataProvider('mapProvider')]
    public function test_loading_map_tracks_whether_it_has_fighting_area(string $map, bool $expected): void
    {
        $service = app(DaqloonFightingAreaService::class);
        $service->track($this->RandomUser->id, ! $expected);

        UserData::where('username', $this->RandomUser->username)->update(['map_location' => $map]);
        $this->get('/world')->assertOk();

        $this->assertSame($expected, $service->isInFightingArea($this->RandomUser->id));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function mapProvider(): array
    {
        return [
            'map with fighting area' => ['4.2', true],
            'map without fighting area' => ['4.3', false],
        ];
    }
}
