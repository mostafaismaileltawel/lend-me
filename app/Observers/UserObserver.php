<?php

namespace App\Observers;

use App\Models\Contact;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(): void
    {
        if(auth()->check())
        {
 Contact::where('user_contact_mobile','=',auth()->user()->phone_number)->update([
            'name' => auth()->user()->name,
            'image' => auth()->user()->image,
        ]);
        }
       
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
