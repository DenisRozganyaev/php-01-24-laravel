<?php

namespace App\Console\Commands\Products;

use App\Models\Product;
use App\Models\WeeklyDiscount as WDModel;
use Illuminate\Console\Command;

class WeeklyDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:weekly-discount
    {--count=5 : Count of products}
    {--min=5 : Minimum % of discount}
    {--max=15 : Minimum % of discount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set discount to random products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        logs()->info('Command was run');
        $count = (int) $this->option('count');
        $min = (int) $this->option('min');
        $max = (int) $this->option('max');

        if ($min < 1 || $max < 1) {
            $this->error('Min/Max values should be more than 0');
        }

        if ($max < $min) {
            $this->error('Maximum value should be more than minimum');
        }

        if ($count < 1) {
            $this->error('Count parameter should be more than 0');
        }

        WDModel::all()->each->delete();

        Product::inRandomOrder()->limit($count)->get()->each(function (Product $product) use ($min, $max) {
            $product->weekly_discount()->create([
                'discount' => rand($min, $max),
            ]);
        });
    }
}
