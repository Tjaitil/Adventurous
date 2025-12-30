<?php

namespace App\Events;

use Auth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Collection<int, \App\Models\Inventory>
     */
    public Collection $Inventory;

    /**
     * Create a new event instance.
     *
     * @param  Collection<int, \App\Models\Inventory>  $Inventory
     */
    public function __construct(Collection $Inventory)
    {
        $this->Inventory = $Inventory;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game-state.'.auth_user()?->id),
        ];
    }
}
