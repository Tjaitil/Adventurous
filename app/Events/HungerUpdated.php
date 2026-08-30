<?php

namespace App\Events;

use App\Models\Hunger;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HungerUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $Hunger;

    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Hunger $Hunger)
    {
        $this->Hunger = $Hunger->current;
        $this->userId = $Hunger->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game-state.'.$this->userId),
        ];
    }
}
