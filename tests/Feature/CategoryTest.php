<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::create(['name' => 'Kategori Uji']);

        $response = $this->actingAs($admin)->get('/categories');

        $response->assertStatus(200);
        $response->assertViewIs('categories.index');
        $response->assertSee('Kategori Uji');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori Baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Kategori Lama']);

        $response = $this->actingAs($admin)->put("/categories/{$category->id}", [
            'name' => 'Kategori Terupdate',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Terupdate']);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Kategori Kosong']);

        $response = $this->actingAs($admin)->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Kategori Berisi']);
        $category->products()->create([
            'name' => 'Produk A',
            'price' => 10000,
            'stock' => 5,
        ]);

        $response = $this->actingAs($admin)->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
