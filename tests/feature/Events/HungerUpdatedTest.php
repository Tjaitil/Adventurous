<?php

namespace Tests\Feature\Events;

use App\Events\HungerUpdated;
use App\Models\Hunger;
use App\Services\HungerService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HungerUpdatedTest extends TestCase
{
    public function test_broadcast_on_returns_private_game_state_channel(): void
    {
        $Hunger = Hunger::factory()->make([
            'current' => 50,
            'user_id' => $this->TestUser->id,
        ]);

        $event = new HungerUpdated($Hunger);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('private-game-state.'.$this->TestUser->id, $channels[0]->name);
        $this->assertEquals(50, $event->Hunger);
        $this->assertEquals($this->TestUser->id, $event->userId);
    }

    public function test_update_hunger_dispatches_hunger_updated_event(): void
    {
        Event::fake([HungerUpdated::class]);
        $this->actingAs($this->TestUser);

        $Hunger = Hunger::factory()->create([
            'current' => 40,
            'user_id' => $this->TestUser->id,
        ]);

        app(HungerService::class)->updateHunger($Hunger, 10);

        Event::assertDispatched(HungerUpdated::class, function (HungerUpdated $event) {
            return $event->userId === $this->TestUser->id
                && $event->Hunger === 50;
        });
    }
}
