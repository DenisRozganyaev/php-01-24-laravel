<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected static ?string $name;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = static::$name ?? fake()->unique()->words(rand(1, 3), true);
        $slug = Str::slug($name);

        return compact('name', 'slug');
    }

    public function withParent(): Factory
    {
        // !is_null(Category::all()->random()) ? Category::all()->random()->id : null
        return $this->state(fn () => ['parent_id' => Category::all()->random()?->id]);
    }
}
