<?php

namespace Tests\Feature\Services;

use App\Events\InventoryUpdated;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->TestUser);
    }

    public function test_editing_inventory_dispatches_inventory_updated_event(): void
    {
        Event::fake([InventoryUpdated::class]);

        $Inventory = Inventory::where('user_id', $this->TestUser->id)->get();

        $service = app(InventoryService::class);
        $service->edit($Inventory, 'potato seed', 5, $this->TestUser->id);

        Event::assertDispatched(InventoryUpdated::class, function (InventoryUpdated $event) use ($Inventory) {
            return $event->Inventory === $Inventory
                && $event->user->id === $this->TestUser->id;
        });
    }
}
