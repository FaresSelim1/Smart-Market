<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create a pending order from the current cart.
     */
    public function createOrderFromCart(array $customerData, float $discount = 0, string $couponCode = null, int $branchId = null): Order
    {
        $items = $this->cartService->getItems();

        if ($items->isEmpty()) {
            throw new \Exception("Cannot create order from empty cart.");
        }

        if (!$branchId) {
            $branchId = session('active_branch_id', \App\Models\Branch::first()?->id);
        }

        if (!$branchId) {
            throw new \Exception('No deployment branch selected.');
        }

        logger()->info('Creating order with branch', [
            'branch_id' => $branchId,
            'customer'  => $customerData['email'] ?? 'guest',
        ]);

        return DB::transaction(function () use ($items, $customerData, $discount, $couponCode, $branchId) {
            // 1. Validate Stock
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalStock = $product->branches->sum('pivot.stock_level');
                
                if ($totalStock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }
            }

            // 2. Calculate Subtotal
            $subtotal = $items->reduce(fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);

            // 3. Create Order
            $order = Order::create([
                'user_id'          => Auth::id(),
                'branch_id'        => $branchId,
                'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount'     => max(0, $subtotal - $discount),
                'discount'         => $discount,
                'coupon_code'      => $couponCode,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'shipping_address' => $customerData['address'] ?? 'Default Address',
                'customer_name'    => $customerData['name'] ?? null,
                'customer_email'   => $customerData['email'] ?? null,
            ]);

            // 4. Create Order Items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            return $order;
        });
    }
}