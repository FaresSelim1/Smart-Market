<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Jobs\SendOrderConfirmation;
use App\Jobs\CheckLowStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Service handling order creation and status management.
 * Encapsulates business logic to keep controllers thin.
 */
class OrderService
{
    /**
     * Create a new order and deduct branch-specific stock.
     *
     * @param array    $data       ['total' => float, 'address' => string, 'discount' => float, 'coupon_code' => string|null]
     * @param array    $cartItems  [product_id => ['quantity' => int, 'price' => float], ...]
     * @param int|null $branchId   The branch to fulfill from
     * @return Order
     */
    public function createOrder(array $data, array $cartItems, $branchId): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $branchId) {
            $order = Order::create([
                'user_id'          => Auth::id(),
                'branch_id'        => $branchId,
                'order_number'     => 'ORD-' . strtoupper(uniqid()),
                'total_amount'     => 0, // Placeholder, calculated below
                'discount'         => $data['discount'] ?? 0,
                'coupon_code'      => $data['coupon_code'] ?? null,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'shipping_address' => $data['address'] ?? 'No Address Provided',
            ]);

            $calculatedSubtotal = 0;

            foreach ($cartItems as $productId => $item) {
                $product = Product::find($productId);

                if ($product) {
                    $price = $item['price'] ?? $product->current_price;
                    $quantity = $item['quantity'];

                    $order->items()->create([
                        'product_id' => $productId,
                        'quantity'   => $quantity,
                        'price'      => $price,
                    ]);

                    $calculatedSubtotal += ($price * $quantity);

                    // Deduct stock specifically for this branch
                    if ($branchId) {
                        DB::table('branch_product')
                            ->where('product_id', $productId)
                            ->where('branch_id', $branchId)
                            ->decrement('stock_level', $quantity);

                        // Dispatch low stock check
                        CheckLowStock::dispatch($productId, $branchId);
                    }
                }
            }

            // Update order with actual total
            $finalTotal = max(0, $calculatedSubtotal - ($data['discount'] ?? 0));
            $order->update(['total_amount' => $finalTotal]);

            return $order;
        });
    }

    /**
     * Update order status with transition validation.
     *
     * @throws \InvalidArgumentException
     */
    public function updateStatus(Order $order, string $status): void
    {
        $order->transitionTo($status);
    }
}