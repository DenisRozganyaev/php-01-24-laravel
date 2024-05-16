<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductsController extends Controller
{
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        $products = Product::orderByDesc('id')->paginate(12);

        return view('products/index', compact('products'));
    }

    public function show(Product $product): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        $gallery = [
            $product->thumbnailUrl,
            ...$product->images->map(fn ($image) => $image->url),
        ];
        $wishes = [
            'price' => auth()->check() ? auth()->user()->isWishedProduct($product) : false,
            'exist' => auth()->check() ? auth()->user()->isWishedProduct($product, 'exist') : false,
        ];

        return view('products/show', compact('product', 'gallery', 'wishes'));
    }
}
