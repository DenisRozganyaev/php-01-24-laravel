<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = ['slug', 'title', 'description', 'SKU', 'price', 'new_price', 'quantity', 'thumbnail'];

    public $sortable = ['id', 'title', 'SKU', 'quantity', 'price', 'created_at', 'finalPrice'];

    public $sortableAs = ['finalPrice'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function finalPrice(): Attribute
    {
        return Attribute::get(fn() => round(
            $this->attributes['new_price'] && $this->attributes['new_price'] > 0 ? $this->attributes['new_price'] : $this->attributes['price'],
            2
        ));
    }
}
