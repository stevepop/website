<?php

namespace App\Listeners;

use App\Events\UserHasSignedUp;
use App\Notifications\UserSignedUpNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyWhenSignedUp
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserHasSignedUp $event)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserHasSignedUp  $event
     * @return void
     */
    public function handle(UserHasSignedUp $event)
    {
        $admins = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 1);
            })->get();

        Notification::send($admins, new UserSignedUpNotification($event->user));
    }
}
