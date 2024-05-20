<?php

namespace App\Listeners;

use App\Models\Amountreq;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\CreateRequestNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SendRequestNotifications
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
        $from =$auth_user->name ? null : $auth_user->phone_number;
        $contact = $event->user;
        $amount = $event->amount;
        $currency=$event->currency;
        $user = Contact::where('user_contact_mobile', $event->user->phone_number)->where('owner_user_mobile', auth()->user()->phone_number)->first();
        $exchange_rate= DB::table('currencies')->where('currency',$currency)->pluck('exchange_rate')->first();
        Amountreq::create(['title' => 'amount request', 'body' => "{$from} need you to lend {$amount}", 'type' => "amount_request",'sender_id'=>$auth_user->id ,'from_country_code'=>$auth_user->country_code,'from_user_mobile' => auth()->user()->phone_number, 'receiver_id'=>$event->user->id,'to_country_code'=>$event->user->country_code,'to_user_mobile' => $event->user->phone_number, 'amount' => $amount,'send_time' =>Carbon::now(),'currency' => $currency, 'exchange_rate'=>$exchange_rate ,'amount_exchange'=>($exchange_rate*$amount)]);

        $user->notify(new CreateRequestNotification($contact, $amount));
    }
}
