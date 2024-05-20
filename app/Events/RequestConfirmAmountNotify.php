<?php

namespace App\Events;

use App\Models\Amountreq;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestConfirmAmountNotify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $amountreq;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Amountreq $amountreq)
    {
        $this->user = $user;
        $this->amountreq = $amountreq;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
