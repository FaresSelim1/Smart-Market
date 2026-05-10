<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
{
    public function collection()
    {
        // Exporting products with their current branch stock levels
        return Product::with('branches')->get();
    }
}