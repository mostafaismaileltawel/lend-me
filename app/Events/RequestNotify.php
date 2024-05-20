<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestNotify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $amount;
    public $currency;
    /**
     * Create a new event instance.
     */
    public function __construct(User $user, $amount ,$currency)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->currency = $currency;
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
