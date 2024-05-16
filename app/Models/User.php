<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'birthdate',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishes(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'wish_list',
            'user_id',
            'product_id'
        )->withPivot(['price', 'exist']);
    }

    public function addToWish(Product $product, string $type = 'price')
    {
        $wished = $this->wishes()->find($product); // wish_list

        if ($wished) {
            $this->wishes()->updateExistingPivot($wished, [$type => true]);
        } else {
            $this->wishes()->attach($product, [$type => true]);
        }
    }

    public function removeFromWish(Product $product, string $type = 'price')
    {
        $this->wishes()->updateExistingPivot($product, [$type => false]);
        $product = $this->wishes()->find($product);

        if ($product->pivot->exist === 0 && $product->pivot->price === 0) {
            $this->wishes()->detach($product);
        }
    }

    public function isWishedProduct(Product $product, string $type = 'price'): bool
    {
        return $this->wishes()
            ->where('product_id', $product->id)
            ->wherePivot($type, true)
            ->exists();
    }
}
