<?php

namespace App\Jobs\Wishlist;

use App\Models\Product;
use App\Notifications\Wishlist\PriceDownNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PriceUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Product $product)
    {
        $this->onQueue('wishilist-notify');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->product->followers()
            ->wherePivot('price', true)
            ->chunk(1_000, function(Collection $users) {
                logs()->info('Price notification send for users = ' . $users->pluck(['id'])->implode(', '));
                Notification::send(
                    $users,
                    app(PriceDownNotification::class, ['product' => $this->product]) // new PriceDownNotification($this->product)
                );
            });
    }
}
