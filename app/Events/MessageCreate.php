<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
/**
 * @var \app\Models\Message
 */
public $message;
    /**
     * Create a new event instance.
     * 
     *  @param \App\Models\Message $message
     * 
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $user=Auth::user();
        $contact =$user->contacts->where('id','<>',$user->id)->first();
        return [
            new PresenceChannel('Message.'. $contact->id),
        ];
    }
}
