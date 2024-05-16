<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\Feature\Traits\SetupTrait;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use SetupTrait;

    public function test_create_product(): void
    {
        $file = UploadedFile::fake()->image('test_image.png');
        $data = array_merge(
            Product::factory()->make()->toArray(),
            ['thumbnail' => $file]
        );

        $slug = Str::slug($data['title']);

        $this->mock(
            FileService::class,
            function (MockInterface $mock) use ($slug) {
                $mock->shouldReceive('upload')
                    ->andReturn("$slug/uploaded_image.png");
            }
        );

        $this->actingAs(User::role('admin')->first())
            ->post(route('admin.products.store'), $data);

        $this->assertDatabaseHas(Product::class, [
            'title' => $data['title'],
            'thumbnail' => "$slug/uploaded_image.png",
        ]);
    }
}
