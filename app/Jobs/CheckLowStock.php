<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Jobs\NotTenantAware;

/**
 * Queued job to check if a product's stock in a branch
 * has fallen below the low_stock_threshold.
 * Dispatched after every stock decrement during order creation.
 */
class CheckLowStock implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId,
        public int $branchId,
    ) {}

    public function handle(): void
    {
        $row = DB::table('branch_product')
            ->where('product_id', $this->productId)
            ->where('branch_id', $this->branchId)
            ->first();

        if (! $row) {
            return;
        }

        if ($row->stock_level <= $row->low_stock_threshold) {
            Log::warning('Low stock alert', [
                'product_id'          => $this->productId,
                'branch_id'           => $this->branchId,
                'stock_level'         => $row->stock_level,
                'low_stock_threshold' => $row->low_stock_threshold,
            ]);

            // In a production system, this would send a notification
            // to admins via email, Slack, etc.
        }
    }
}
