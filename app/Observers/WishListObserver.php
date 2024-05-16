<?php

namespace App\Observers;

use App\Jobs\Wishlist\PriceUpdatedJob;
use App\Jobs\Wishlist\ProductExistsJob;
use App\Models\Product;

class WishListObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->finalPrice < $product->getOriginal('finalPrice')) {
            PriceUpdatedJob::dispatch($product);
        }
        if ($product->isExists && !$product->getOriginal('isExists')) {
            ProductExistsJob::dispatch($product);
        }
    }
}
