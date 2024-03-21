<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\UpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contract\ProductRepositoryContract;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories'])->sortable()->paginate(8);

        return view('admin/products/index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin/products/create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, ProductRepositoryContract $repository)
    {
        if ($product = $repository->create($request)) {
            notify()->success("Product '$product->title' was successfully created");
            return redirect()->route('admin.products.index');
        }

        notify()->danger("Oops smth went wrong");

        return redirect()->back()->withInput();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['images', 'categories']);

        $categories = Category::all();
        $productCategories = $product->categories->pluck('id')->toArray();

        return view('admin/products/edit', compact('categories', 'productCategories', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Product $product, ProductRepositoryContract $repository)
    {

        if ($repository->update($product, $request)) {
            notify()->success("Product '$product->title' was successfully update");
            return redirect()->route('admin.products.edit', $product);
        }

        notify()->danger("Oops smth went wrong");

        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->categories()->detach();
        $product->delete();

        return redirect()->route('admin.products.index');
    }
}
