<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyDiscount extends Model
{
    use HasFactory;

    protected $table = 'weekly_discount';

    protected $fillable = ['id', 'product_id', 'discount', 'created_at', 'updated_at'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
