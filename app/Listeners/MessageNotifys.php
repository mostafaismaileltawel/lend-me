<?php

namespace App\Listeners;

use App\Models\Contact;
use App\Notifications\MessagesNotification;
use Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MessageNotifys
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $contact = $event->user;
       $user = Contact::where('id', $event->user->id)->where('owner_user_mobile', auth()->user()->phone_number)->first();
    
       $user->notify(new MessagesNotification($contact));
    
    }
}
