<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\RefusedAddContactNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefusedContactNotifications
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
        $auth_user = Auth::user();
        $contact = $event->user;
        $user = User::where('id', $event->user->id)->first();
        DB::table('requests')->where('receiver_id', auth()->user()->id)->where('sender_id', $event->user->id)->update(['status' => 'refused', 'edited_time' => Carbon::now()]);
        $user->notify(new RefusedAddContactNotifications($contact));
    }
}