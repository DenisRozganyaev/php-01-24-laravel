<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::take(5)->get();
        $products = Product::available()->orderByDesc('id')->take(8)->get();

        return view('home', compact('categories', 'products'));
    }
}
