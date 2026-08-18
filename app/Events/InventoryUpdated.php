<?php

namespace App\Events;

use App\Http\Resources\InventoryResourceCollection;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Collection<int, Inventory>
     */
    public Collection $Inventory;

    public User $user;

    /**
     * Create a new event instance.
     *
     * @param  Collection<int, Inventory>  $Inventory
     */
    public function __construct(Collection $Inventory, User $user)
    {
        $this->Inventory = $Inventory;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game-state.'.$this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return new InventoryResourceCollection($this->Inventory->loadMissing('item'))->resolve();
    }
}
