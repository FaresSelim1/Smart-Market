<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Service for product catalog operations.
 * Supports multi-branch stock queries with caching.
 */
class ProductService
{
    /**
     * Cache TTL in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Fetch products for catalog view with caching.
     *
     * @param mixed   $catalogBranchId null = Global, otherwise selected branch id
     * @param string  $search          Search query string
     * @param mixed   $categoryId      null = All, otherwise selected category id
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableProducts($catalogBranchId = null, string $search = '', $categoryId = null)
    {
        // Defensive normalization: Livewire often passes empty strings or numeric strings
        $catalogBranchId = (is_numeric($catalogBranchId) && (int) $catalogBranchId > 0) 
            ? (int) $catalogBranchId 
            : null;

        $categoryId = (is_numeric($categoryId) && (int) $categoryId > 0)
            ? (int) $categoryId
            : null;

        $cacheKey = "products:branch:{$catalogBranchId}:category:{$categoryId}:search:" . md5($search);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($catalogBranchId, $search, $categoryId) {
            return $this->queryProducts($catalogBranchId, $search, $categoryId);
        });
    }

    /**
     * Execute the actual product query.
     */
    private function queryProducts($catalogBranchId, string $search, $categoryId)
    {
        $query = Product::query();

        // Category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Search filter (applies to both modes)
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.description', 'like', '%' . $search . '%');
            });
        });

        if ($catalogBranchId === null) {
            // Global catalog: SUM stock across all branches
            return $query
                ->select(
                    'products.id',
                    'products.category_id',
                    'products.name',
                    'products.description',
                    'products.price',
                    'products.sku',
                    'products.image_path',
                    'products.created_at',
                    'products.updated_at',
                    DB::raw('COALESCE(SUM(branch_product.stock_level), 0) as stock')
                )
                ->leftJoin('branch_product', 'products.id', '=', 'branch_product.product_id')
                ->groupBy(
                    'products.id',
                    'products.category_id',
                    'products.name',
                    'products.description',
                    'products.price',
                    'products.sku',
                    'products.image_path',
                    'products.created_at',
                    'products.updated_at'
                )
                ->with('category', 'images', 'flashSales')
                ->get();
        }

        // Branch catalog: pivot stock only for the selected branch
        return $query
            ->select('products.*', 'branch_product.stock_level as stock')
            ->join('branch_product', 'products.id', '=', 'branch_product.product_id')
            ->where('branch_product.branch_id', $catalogBranchId)
            ->with('category', 'images', 'flashSales')
            ->get();
    }

    /**
     * Check stock level in the specific branch pivot.
     */
    public function checkStock(int $productId, int $branchId, int $quantity): bool
    {
        $stock = DB::table('branch_product')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->value('stock_level');

        return $stock >= $quantity;
    }

    /**
     * Decrement stock in the specific branch pivot.
     */
    public function decrementStock(int $productId, int $branchId, int $quantity): void
    {
        DB::table('branch_product')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->decrement('stock_level', $quantity);

        // Invalidate cache after stock change
        $this->clearCache($branchId);
    }

    /**
     * Clear product listing cache for a specific branch (or all).
     */
    public function clearCache(?int $branchId = null): void
    {
        // Clear the known cache keys
        Cache::forget("products:branch:{$branchId}:search:" . md5(''));
        Cache::forget("products:branch::search:" . md5(''));
    }
}