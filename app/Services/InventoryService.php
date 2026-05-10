<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

/**
 * Service for inventory management operations.
 * Handles stock level updates for branch-product pivot.
 */
class InventoryService
{
    public function __construct(protected ProductRepository $productRepo) {}

    /**
     * Update stock level for a product in a specific branch.
     *
     * @param int $productId
     * @param int $branchId
     * @param int $quantity  Positive to add, negative to deduct
     * @return int Number of affected rows
     */
    public function updateStock(int $productId, int $branchId, int $quantity): int
    {
        return DB::table('branch_product')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->update(['stock_level' => DB::raw('stock_level + ' . (int) $quantity)]);
    }
}