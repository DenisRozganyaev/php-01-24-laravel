<?php

namespace App\Events\Admin;

use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class NotifyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(public string $message)
    {
        $this->broadcastVia('reverb');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notify.admin';
    }
}
