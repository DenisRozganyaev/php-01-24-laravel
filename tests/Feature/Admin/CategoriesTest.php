<?php

namespace Tests\Feature\Admin;

use App\Enums\Roles;
use App\Models\Category;
use Tests\Feature\Traits\SetupTrait;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use SetupTrait;

    public function test_allow_see_categories_with_role_admin()
    {
        $categories = Category::factory(2)->create();

        $response = $this->actingAs($this->user())
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index');
        $response->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_allow_see_categories_with_role_moderator()
    {
        $categories = Category::all()->pluck('name')->toArray();

        $response = $this->actingAs($this->user(Roles::MODERATOR))
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index');
        $response->assertSeeInOrder($categories);
    }

    public function test_does_not_allow_see_categories_with_role_customer()
    {
        $response = $this->actingAs($this->user(Roles::CUSTOMER))
            ->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_create_category_with_valid_data()
    {
        $data = Category::factory()->make()->toArray();

        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name'],
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.index');

        $response->assertSessionHas('toasts');
        $response->assertSessionHas('toasts', function ($collection) use ($data) {
            return $collection->first()['message'] === "Category [{$data['name']}] was created.";
        });

        $this->assertDatabaseHas(Category::class, [
            'name' => $data['name'],
        ]);
    }

    public function test_does_not_create_category_with_invalid_data()
    {
        $data = ['name' => 'a'];

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name'],
        ]);
    }

    public function test_update_category_with_valid_data()
    {
        $category = Category::factory()->create();
        $parent = Category::factory()->create();

        $response = $this->actingAs($this->user())
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'parent_id' => $parent->id,
            ]);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.edit', compact('category'));

        $category->refresh();

        $this->assertEquals($category->parent_id, $parent->id);

        $response->assertSessionHas('toasts');
        $response->assertSessionHas('toasts', function ($collection) use ($category) {
            return $collection->first()['message'] === "Category [{$category->name}] was updated.";
        });
    }

    public function test_remove_category()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id,
        ]);

        $response = $this->actingAs($this->user())
            ->delete(route('admin.categories.destroy', $category));

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id,
        ]);
    }
}
