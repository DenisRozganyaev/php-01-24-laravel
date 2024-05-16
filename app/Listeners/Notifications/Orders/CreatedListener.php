<?php

namespace App\Listeners\Notifications\Orders;

use App\Events\OrderCreated;
use App\Notifications\Admin\OrderCreatedNotification;
use Illuminate\Support\Facades\Notification;

class CreatedListener
{
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
    public function handle(OrderCreated $event): void
    {
        logs()->info('listener triggered');

        Notification::send(
            \App\Models\User::role('admin')->get(),
            app(OrderCreatedNotification::class, ['order' => $event->order])
        );
    }
}
