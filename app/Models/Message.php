<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Message extends Model
{

    use HasFactory;
    protected $timestamp=false;
    protected $fillable = ['body','type','localpath','size','name','file_path','sender_id','receiver_id','from_phone_number','to_phone_number','send_time','edited_time','date'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Contact::class, 'receiver_id');
    }


}
