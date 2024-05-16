<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

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
            ...$product->images->map(fn($image) => $image->url)
        ];

        return view('products/show', compact('product', 'gallery'));
    }
}
