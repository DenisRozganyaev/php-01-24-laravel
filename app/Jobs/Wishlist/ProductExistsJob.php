<?php

namespace App\Jobs\Wishlist;

use App\Models\Product;
use App\Notifications\Wishlist\ProductAvailableNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProductExistsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'wishilist-notify';

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
            ->wherePivot('exist', true)
            ->chunk(1_000, function(Collection $users) {
                logs()->info('Exist notification send for users = ' . $users->pluck(['id'])->implode(', '));
                Notification::send(
                    $users,
                    app(ProductAvailableNotification::class, ['product' => $this->product]) // new PriceDownNotification($this->product)
                );
            });
    }
}
