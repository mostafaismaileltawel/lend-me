<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amountreq extends Model
{
    use HasFactory;
    protected $table = 'requests';
    public $timestamps=false;
  
    protected $fillable = [
        'id',
        'title',
        'body',
        'type',
        'from_user_mobile',
        'to_user_mobile',
        'status',
        'amount',
        'send_time',
        'edited_time',
        'sender_id',
        'receiver_id',
        'from_country_code',
        'to_country_code',
        'currency',
        'currency_base' ,
        'amount_exchange',
        'exchange_rate' ,
    ];
    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Contact::class, 'receiver_id');
    }
}
