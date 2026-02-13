<?php

namespace App\Events;

use App\Http\Resources\DiplomacyResource;
use App\Models\Diplomacy;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiplomacyUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array<string, float>
     */
    public array $Diplomacy;

    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Diplomacy $Diplomacy, int $userId)
    {
        $this->Diplomacy = (new DiplomacyResource($Diplomacy))->resolve();
        $this->userId = $userId;
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
