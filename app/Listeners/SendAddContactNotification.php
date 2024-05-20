<?php

namespace App\Listeners;

use App\Events\ContactAddNtify;
use App\Models\User;
use App\Notifications\CreateAddContactNotificactio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SendAddContactNotification
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
    public function handle(ContactAddNtify $event): void
    {
        $auth_user = Auth::user();
        $from = $auth_user->name ? null : $auth_user->phone_number;

        $contact = $event->user;
        $user = User::where('id', $event->user->id)->first();
        DB::table('requests')->insert(['title' => 'add request', 'body' => "{$from} invite you to contact", 'type' => "add_contact", 'sender_id' => auth()->user()->id,'from_country_code'=>$auth_user->country_code,'from_user_mobile' => $auth_user->phone_number,'to_country_code'=>$event->user->country_code,'to_user_mobile' => $event->user->phone_number, 'receiver_id' => $event->user->id, 'send_time' => Carbon::now()]);
        $user->notify(new CreateAddContactNotificactio($contact));

    }
}
