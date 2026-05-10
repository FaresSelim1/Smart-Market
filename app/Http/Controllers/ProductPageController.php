<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductPageController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['category', 'images']);


        return view('products.show', [
            'product' => $product,
        ]);
    }
}


