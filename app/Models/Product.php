<?php

namespace App\Models;

use App\Observers\WishListObserver;
use App\Services\Contract\FileServiceContract;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

#[ObservedBy([WishListObserver::class])]
class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = ['slug', 'title', 'description', 'SKU', 'price', 'new_price', 'quantity', 'thumbnail'];

    public $sortable = ['id', 'title', 'SKU', 'quantity', 'price', 'created_at', 'finalPrice'];

    protected $with = ['weekly_discount'];

    public function getRouteKeyName()
    {
        return request()->expectsJson() ? 'id' : 'slug';
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'wish_list',
            'product_id',
            'user_id'
        );
    }

    public function weekly_discount(): HasOne
    {
        return $this->hasOne(WeeklyDiscount::class);
    }

    public function scopeAvailable(Builder $query)
    {
        $query->where('quantity', '>', 0);
    }

    public function setThumbnailAttribute($image)
    {
        $fileService = app(FileServiceContract::class);

        if (! empty($this->attributes['thumbnail'])) {
            $fileService->remove($this->attributes['thumbnail']);
        }

        if (request()->expectsJson()) {
            $this->attributes['thumbnail'] = $image;
        } else {
            $this->attributes['thumbnail'] = $fileService->upload(
                $image,
                $this->attributes['slug']
            );
        }
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(function () {
            $key = 'products.thumbnail.'.$this->getAttribute('id');

            if (! Storage::has($this->attributes['thumbnail'])) {
                return $this->attributes['thumbnail'];
            }

            if (! Cache::has($key)) {
                $link = Storage::temporaryUrl($this->attributes['thumbnail'], now()->addMinutes(10));
                Cache::put($key, $link, 590);
            }

            return Cache::get($key);
        });
    }

    public function finalPrice(): Attribute
    {
        return Attribute::get(function () {
            $price = $this->attributes['new_price'] && $this->attributes['new_price'] > 0 ? $this->attributes['new_price'] : $this->attributes['price'];

            $wdRow = $this->weekly_discount;

            if ($wdRow) {
                $price -= ($price / 100 * $wdRow->getAttribute('discount'));
            }

            return round($price, 2);
        });
    }

    public function discount(): Attribute
    {
        return Attribute::get(function () {
            $price = $this->getAttribute('price');
            $newPrice = $this->getAttribute('new_price');

            if (! $newPrice) {
                return null;
            }

            $result = ($price - $newPrice) / ($price / 100);

            return round($result, 2);
        });
    }

    public function isExists(): Attribute
    {
        return Attribute::get(fn () => $this->attributes['quantity'] > 0);
    }

    public function rowId(): Attribute
    {
        return Attribute::get(fn () => Cart::instance('cart')->content()->where('id', '=', $this->id)?->first()?->rowId);
    }

    public function isInCart(): Attribute
    {
        return Attribute::get(fn () => (bool) $this->rowId);
    }
}
