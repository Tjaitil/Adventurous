<?php

namespace App\Events;

use App\Models\Miner;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermitsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $location;

    public int $permits;

    public int $userId;

    public function __construct(Miner $Miner)
    {
        $this->location = $Miner->location;
        $this->permits = $Miner->permits;
        $this->userId = $Miner->user_id;
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game-state.'.$this->userId),
        ];
    }
}
