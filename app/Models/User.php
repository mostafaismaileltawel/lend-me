<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    //  protected static function boot()
    //  {
    //      parent::boot();

    //      static::saved(function ($user) {
    //          $user->contacts()->sync($user->contacts->pluck('user_id'));
    //      });
    //  }
   
    protected $fillable = [
        'name',
        'phone_number',
        'phone_verified',
        'verification_code',
        'borrow_amount',
        'lend_amount',
        'expire_at',
        'image',
        'token_device',
        'country_code',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'phone_verified',
        'verification_code',
        'borrow_amount',
        'lend_amount',
        'expire_at',
        'token_device',
        "created_at",
        "updated_at",
        'token',
       

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Summary of contacts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class,'owner_user_mobile','phone_number');
    }

    public function requestes()
    {
        return $this->hasMany(Amountreq::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
public function transactio()
{
    return $this->hasMany(Transaction::class,'from_phone_number','phone_number');
}


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
