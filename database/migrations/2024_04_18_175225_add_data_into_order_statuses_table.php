<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach(OrderStatusEnum::cases() as $case) {
            \App\Models\OrderStatus::firstOrCreate(['name' => $case->value]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach(OrderStatusEnum::cases() as $case) {
            \App\Models\OrderStatus::all()->each->delete();
        }
    }
};
