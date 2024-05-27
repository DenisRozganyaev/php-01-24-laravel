<?php

namespace App\Models;

use App\Services\Contract\FileServiceContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'imageable_id', 'imageable_type'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): Attribute
    {
        return Attribute::get(function () {
            $key = 'products.images.'.$this->getAttribute('path');

            if (! Cache::has($key)) {
                $link = Storage::temporaryUrl($this->attributes['path'], now()->addMinutes(10));
                Cache::put($key, $link, 590);
            }

            return Cache::get($key);
        });
    }

    public function setPathAttribute($path)
    {
        $this->attributes['path'] = app(FileServiceContract::class)->upload(
            $path['image'],
            $path['directory'] ?? null
        );
    }
}
