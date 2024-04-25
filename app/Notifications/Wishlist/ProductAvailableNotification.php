<?php

namespace App\Notifications\Wishlist;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductAvailableNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Product $product)
    {
        $this->onQueue('wishilist-notify');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $user): MailMessage
    {
        return (new MailMessage)
            ->line('Hey, ' . $user->name . ' ' . $user->lastname)
            ->line('Product from your wish list has lower price!')
            ->line('Hurry up!')
            ->line('Product: ' . $this->product->title)
            ->action('Go to product page', url(route('products.show', $this->product)))
            ->line('Thank you for using our application!');
    }
}
