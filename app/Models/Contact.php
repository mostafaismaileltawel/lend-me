<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Contact extends Model
{
    use HasFactory, Notifiable;
 
    protected $fillable = [
        'id',
        'name',
        'image',
        'user_contact_countrycode',
        'user_contact_mobile',
        'owner_contact_countrycode',
        'owner_user_mobile',
        'total_amount',
        'currency_base'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,);
    }
}
