<?php

namespace App\Notifications\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Order $order){}

    public function viaQueues(): array
    {
        return [
            'telegram' => 'order-created',
            'mail' => 'order-created',
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $user): array
    {
        return $user->telegram_id ? ['telegram', 'mail'] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        logs()->info('notify admin by email');
        $url = route('admin.dashboard');

        return (new MailMessage)
                    ->line("Hello, $notifiable->name $notifiable->lastname!")
                    ->line("There is a new order")
                    ->line('')
                    ->line("Total: " . $this->order->total)
                    ->line('')
                    ->line('You can check it in admin panel')
                    ->action('Admin dashboard', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toTelegram(User $notifiable)
    {
        logs()->info('notify admin by telegram');
        $url = route('admin.dashboard');

        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content("Hello, $notifiable->name $notifiable->lastname!")
            ->line("There is a new order")
            ->line('')
            ->line("Total: " . $this->order->total)
            ->line('')
            ->line('You can check it in admin panel')
            ->button('Admin panel', $url);
    }
}
