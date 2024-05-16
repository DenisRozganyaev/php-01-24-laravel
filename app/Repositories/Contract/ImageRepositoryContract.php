<?php

namespace App\Repositories\Contract;

use Illuminate\Database\Eloquent\Model;

interface ImageRepositoryContract
{
    // Product => $product->images
    // User
    public function attach(Model $model, string $relation, array $images = [], ?string $directory = null): void;
}
