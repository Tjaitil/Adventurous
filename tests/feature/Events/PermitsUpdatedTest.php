<?php

namespace Tests\Feature\Events;

use App\Events\PermitsUpdated;
use App\Models\Miner;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

class PermitsUpdatedTest extends TestCase
{
    public function test_broadcast_on_returns_private_game_state_channel(): void
    {
        $Miner = Miner::where('user_id', $this->TestUser->id)->firstOrFail();
        $Miner->permits = 25;

        $event = new PermitsUpdated($Miner);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('private-game-state.'.$this->TestUser->id, $channels[0]->name);
        $this->assertEquals($Miner->location, $event->location);
        $this->assertEquals(25, $event->permits);
    }
}
