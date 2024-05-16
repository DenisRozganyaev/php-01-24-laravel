<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('categories/index');
    }

    public function show(Category $category): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('categories/show');
    }
}
