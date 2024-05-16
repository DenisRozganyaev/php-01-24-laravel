<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use HasFactory, Sortable;

    protected $fillable = ['id', 'name', 'slug', 'parent_id', 'created_at', 'updated_at'];

    public $sortable = ['id', 'name', 'parent.name'];

    public $sortableAs = ['products_count', 'childs_count'];

    // $category = Category::find(4);
    // $category->parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // $category = Category::find(4);
    // $category->childs
    // $category->childs()->whereIn('name', [.....])->get()
    public function childs(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
