<?php

namespace App\Listeners\Notifications\Users;

use App\Events\Users\PasswordNotification;
use App\Notifications\Users\PasswordGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class PasswordGeneratedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordNotification $event): void
    {
        Notification::send(
            $event->user,
            app(PasswordGeneratedNotification::class, ['password' => $event->password])
        );
    }
}
