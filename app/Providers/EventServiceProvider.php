<?php

namespace App\Providers;

use App\Events\PostCreated;
use App\Events\PostLiked;
use App\Events\PostRePosted;
use App\Events\PostRepliedTo;
use App\Events\UserFollowed;
use App\Listeners\SendPostCreatedNotifications;
use App\Listeners\SendPostLikedNotification;
use App\Listeners\SendPostRepliedToNotification;
use App\Listeners\SendNewFollowerNotification;
use App\Listeners\SendRePostNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        PostCreated::class => [
            SendPostCreatedNotifications::class,
        ],
        PostLiked::class=>[
            SendPostLikedNotification::class,
        ],
        PostRepliedTo::class=>[
            SendPostRepliedToNotification::class,
        ],
        UserFollowed::class=>[
            SendNewFollowerNotification::class,
        ],
        PostRePosted::class=>[
            SendRePostNotification::class
        ]
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
