<?php

namespace App\Observers;

use App\Models\Product;

class WishListObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->finalPrice < $product->getOriginal('finalPrice')) {
            // Call Price Job
        }
        if ($product->isExists && !$product->getOriginal('isExists')) {
            // Call Price Job
        }
    }
}
