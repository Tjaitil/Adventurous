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
     * @var Collection<int, Inventory>
     */
    public Collection $Inventory;

    /**
     * Create a new event instance.
     *
     * @param  Collection<int, Inventory>  $Inventory
     */
    public function __construct($Inventory)
    {
        $this->Inventory = $Inventory;
        var_dump('InventoryUpdated event fired');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game-state.'.Auth::user()->id),
        ];
    }
}
