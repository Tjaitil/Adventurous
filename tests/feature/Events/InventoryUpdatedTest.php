<?php

namespace Tests\Feature\Events;

use App\Events\InventoryUpdated;
use App\Http\Resources\InventoryResourceCollection;
use App\Models\Inventory;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryUpdatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->TestUser);
    }

    public function test_broadcast_on_and_broadcast_with_return_channel_and_inventory_data(): void
    {
        $this->insertItemToInventory($this->TestUser, 'potato seed', 3);

        $Inventory = Inventory::where('user_id', $this->TestUser->id)->get();

        $event = new InventoryUpdated($Inventory, $this->TestUser);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('private-game-state.'.$this->TestUser->id, $channels[0]->name);

        $expected = (new InventoryResourceCollection($Inventory->loadMissing('item')))->resolve();

        $this->assertEquals($expected, $event->broadcastWith());
    }
}
