<?php

namespace App\Providers;

use App\Events\PostWasCreated;
use App\Notifications\NewPostNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Slack\SlackExtendSocialite@handle',
        ],
        \App\Events\UserHasSignedUp::class => [
            \App\Listeners\NotifyWhenSignedUp::class,
        ],
        PostWasCreated::class => [
            NewPostNotification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
