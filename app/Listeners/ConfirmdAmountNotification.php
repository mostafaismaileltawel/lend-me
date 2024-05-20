<?php

namespace App\Listeners;

use App\Models\Amountreq;
use App\Models\Contact;
use App\Models\Transaction;
use App\Notifications\ConfirmAmountNotifications;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfirmdAmountNotification
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
        $req = $event->amountreq;
        $user = Contact::where('user_contact_mobile',$event->user->phone_number)->where('owner_user_mobile',auth()->user()->phone_number)->first();      

        $auth_user_amount =Contact::where('user_contact_mobile', auth()->user()->phone_number)->where('owner_user_mobile', $event->user->phone_number)->first();  
        $exchange_rate= DB::table('currencies')->where('currency',$req->currency)->pluck('exchange_rate')->first();
        Amountreq::where('id',$req->id)->where('receiver_id',auth()->user()->id)->where('sender_id',$event->user->id)->update(['status' =>'confirmed','edited_time'=> Carbon::now()]);
        Transaction::create(['from_country_code'=> auth()->user()->country_code,'from_phone_number' => auth()->user()->phone_number,'sender_id'=>auth()->user()->id ,'to_country_code'=>$event->user->country_code,'to_phone_number' =>$event->user->phone_number,'receiver_id'=>$event->user->id,'amount' =>$req->amount,'currency_base'=>$req->currency_base,'currency_send'=>$req->currency,'exchange_rate'=>$exchange_rate,'amount_exchange'=>($exchange_rate*$req->amount),'status' =>'confirmed','send_time'=>$req->send_time,'confirm_time'=> Carbon::now()]);

        

        Contact::where('user_contact_mobile',$event->user->phone_number)->where('owner_user_mobile', auth()->user()->phone_number)->update([
'total_amount'=>($user->total_amount-$req->amount_exchange),
'currency_base'=>$req->currency_base,
        ]);
        Contact::where('user_contact_mobile', auth()->user()->phone_number)->where('owner_user_mobile', $event->user->phone_number)->update([
            'total_amount'=>($auth_user_amount->total_amount+$req->amount_exchange) ,
            'currency_base'=>$req->currency_base,

        ]);
        $user->notify(new ConfirmAmountNotifications($contact));
    
}
}
