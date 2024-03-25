<?php

namespace App\Models;

use App\Services\Contract\FileServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = ['slug', 'title', 'description', 'SKU', 'price', 'new_price', 'quantity', 'thumbnail'];

    public $sortable = ['id', 'title', 'SKU', 'quantity', 'price', 'created_at', 'finalPrice'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function scopeAvailable(Builder $query)
    {
        $query->where('quantity', '>', 0);
    }

    public function setThumbnailAttribute($image)
    {
        $fileService = app(FileServiceContract::class);

        if (!empty($this->attributes['thumbnail'])) {
            $fileService->delete($this->attributes['thumbnail']);
        }

        $this->attributes['thumbnail'] = $fileService->upload(
            $image,
            $this->attributes['slug']
        );
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!Storage::has($this->attributes['thumbnail'])) {
                return $this->attributes['thumbnail'];
            }

            return Storage::url($this->attributes['thumbnail']);
        });
    }

    public function finalPrice(): Attribute
    {
        return Attribute::get(fn() => round(
            $this->attributes['new_price'] && $this->attributes['new_price'] > 0 ? $this->attributes['new_price'] : $this->attributes['price'],
            2
        ));
    }
}
