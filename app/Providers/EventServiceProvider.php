<?php

namespace App\Providers;

use App\Events\ContactAddNtify;
use App\Events\ContactConfirmNotify;
use App\Events\ContactRefusedNotify;
use App\Events\MessageNotify;
use App\Events\RequestConfirmAmountNotify;
use App\Events\RequestNotify;
use App\Events\RequestRefusedAmountNotify;
use App\Listeners\ConfirmContactNotification;
use App\Listeners\ConfirmdAmountNotification;
use App\Listeners\MessageNotifys;
use App\Listeners\RefusedAmountNotifications;
use App\Listeners\RefusedContactNotifications;
use App\Listeners\SendAddContactNotification;
use App\Listeners\SendRequestNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ContactAddNtify::class=>[
            SendAddContactNotification::class,
        ],
        ContactRefusedNotify::class=>[
RefusedContactNotifications::class
        ],
        RequestRefusedAmountNotify::class=>[
RefusedAmountNotifications::class,
        ],
        RequestNotify::class=>[
            SendRequestNotifications::class,
        ],
        ContactConfirmNotify::class=>[
            ConfirmContactNotification::class,
        ],
        RequestConfirmAmountNotify::class=>[
            ConfirmdAmountNotification::class,
        ],
        MessageNotify::class=>[MessageNotifys::class ],
       
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
