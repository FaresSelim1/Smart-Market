<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Import products from an Excel file.
 * Expected columns: name, description, price, sku, category
 */
class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $category = Category::firstOrCreate(
            ['slug' => \Str::slug($row['category'] ?? 'general')],
            ['name' => $row['category'] ?? 'General']
        );

        return Product::updateOrCreate(
            ['sku' => $row['sku']],
            [
                'name'        => $row['name'],
                'description' => $row['description'] ?? '',
                'price'       => $row['price'],
                'category_id' => $category->id,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sku'   => 'required|string|max:255',
        ];
    }
}
