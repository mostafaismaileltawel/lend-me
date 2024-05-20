<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Transaction extends Model
{
  public $timestamps=false;

    use HasFactory,SoftDeletes;
    protected $fillable = [
        'from_phone_number',
        'to_phone_number',
        'amount',
      'status',
      'send_time',
      'confirm_time',
        'sender_id',
        'receiver_id',
        'from_country_code',
        'to_country_code',  
        'currency_send',
        'exchange_rate' ,
        'currency_base' ,
        'amount_exchange'
    ];
      protected $table = 'transactions';



      public function user()
{
    return $this->belongsTo(User::class);
}
    }
