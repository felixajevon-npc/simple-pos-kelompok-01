<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row): ?\Illuminate\Database\Eloquent\Model
    {
        return new Product([
            'name'        => $row['name'] ?? $row['nama'],
            'category_id' => $row['category_id'] ?? $row['kategori_id'] ?? 1,
            'price'       => $row['price'] ?? $row['harga'] ?? 0,
            'stock'       => $row['stock'] ?? $row['stok'] ?? 0,
            'sku'         => $row['sku'] ?? null,
            'is_active'   => $row['is_active'] ?? 1,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}