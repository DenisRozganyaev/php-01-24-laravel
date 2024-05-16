<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\CreateRequest;
use App\Http\Requests\Admin\Categories\UpdateRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent'])
            ->withCount(['products', 'childs'])
            ->sortable()
            ->paginate(8);

        return view('admin/categories/index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin/categories/create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::of($data['name'])->slug()->value();

        $category = Category::create($data);

        notify()->success("Category [$category->name] was created.");

        return redirect()->route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $dropdown = Category::where('id', '!=', $category->id)->get();

        return view('admin/categories/edit', compact('category', 'dropdown'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['slug'] = Str::of($data['name'])->slug()->value();

        $category->updateOrFail($data);

        notify()->success("Category [$category->name] was updated.");

        return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->middleware('permission:'.\App\Enums\Permissions\Category::DELETE->value);

        if ($category->childs()->exists()) {
            $category->childs()->update(['parent_id' => null]);
        }

        $category->deleteOrFail();

        notify()->success("Category [$category->name] was removed.");

        return redirect()->route('admin.categories.index');
    }
}
