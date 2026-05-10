<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

/**
 * Repository for Product data access.
 * Implements the repository pattern as required by project criteria.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products available in a specific branch with their stock levels.
     */
    public function getBranchProducts($branchId)
    {
        return Product::whereHas('branches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with(['branches' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }, 'category', 'images', 'flashSales'])->get();
    }

    /**
     * Find a product by its SKU.
     */
    public function findBySku($sku)
    {
        return Product::where('sku', $sku)->firstOrFail();
    }
}