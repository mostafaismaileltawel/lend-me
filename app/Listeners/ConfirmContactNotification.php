<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\ConfirmAddContactNotifications;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ConfirmContactNotification
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
        DB::table('requests')->where('to_user_mobile', auth()->user()->phone_number)->where('from_user_mobile', $event->user->phone_number)->update(['status' => 'confirmed','edited_time'=> Carbon::now()]);
        $user->notify(new ConfirmAddContactNotifications($contact));
    }
}
