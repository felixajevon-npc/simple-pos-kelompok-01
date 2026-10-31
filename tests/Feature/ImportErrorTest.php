<?php

namespace Tests\Feature;

use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\ImportError;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_import_page_with_import_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        ImportError::create([
            'row_number' => 3,
            'error_message' => 'The selected category id is invalid.',
        ]);

        $response = $this->actingAs($admin)->get('/import');

        $response->assertStatus(200);
        $response->assertSee('Daftar Baris Gagal Import');
        $response->assertSee('The selected category id is invalid.');
        $response->assertSee('3');
    }

    public function test_import_job_records_errors_for_invalid_rows(): void
    {
        Storage::fake();

        $category = Category::create(['name' => 'Minuman']);

        $csvContent = "name,category_id,price\n" .
                      "Kopi Susu,{$category->id},15000\n" .
                      "Produk Gagal,999,10000\n" .
                      "Kolom Kurang\n";

        $csvPath = 'imports/test_products.csv';
        Storage::put($csvPath, $csvContent);

        (new ProcessProductImport($csvPath))->handle();

        $this->assertDatabaseHas('products', ['name' => 'Kopi Susu']);
        $this->assertDatabaseMissing('products', ['name' => 'Produk Gagal']);

        $this->assertDatabaseHas('import_errors', [
            'row_number' => 3,
        ]);
        $this->assertDatabaseHas('import_errors', [
            'row_number' => 4,
            'error_message' => 'Jumlah kolom tidak sesuai dengan header CSV.',
        ]);
    }
}
