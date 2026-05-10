<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\DiscountService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

class Checkout extends Component
{
    public $items = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;
    public $couponCode = '';
    public $shippingAddress = '';
    
    public $couponError = '';
    public $couponMessage = '';

    public function mount(CartService $cartService)
    {
        if ($cartService->getCount() === 0) {
            return redirect()->route('cart');
        }

        $this->loadCart($cartService);
    }

    public function loadCart(CartService $cartService)
    {
        $this->items = $cartService->getItems()->toArray();
        $this->subtotal = array_reduce($this->items, fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = $this->subtotal - $this->discount;
    }

    public function applyCoupon(DiscountService $discountService)
    {
        $this->couponError = '';
        $this->couponMessage = '';

        try {
            $result = $discountService->applyCoupon($this->couponCode, $this->subtotal);
            $this->discount = $result['discount'];
            $this->calculateTotal();
            $this->couponMessage = "Coupon applied! You saved $" . number_format($this->discount, 2);
        } catch (\Exception $e) {
            $this->couponError = $e->getMessage();
            $this->discount = 0;
            $this->calculateTotal();
        }
    }

    /**
     * Process the final order.
     */
    public function processOrder(OrderService $orderService, PaymentService $paymentService)
    {
        $this->validate([
            'shippingAddress' => 'required|string|min:10',
        ], [
            'shippingAddress.required' => 'Please provide a delivery address.',
            'shippingAddress.min' => 'Address is too short.',
        ]);

        try {
            // Use session branch or default
            $branchId = session('active_branch_id', \App\Models\Branch::first()?->id);

            $order = $orderService->createOrder(
                [
                    'total' => $this->total,
                    'discount' => $this->discount,
                    'coupon_code' => $this->couponCode,
                    'address' => $this->shippingAddress,
                ],
                // Format items for OrderService
                collect($this->items)->mapWithKeys(fn($item) => [
                    $item['product_id'] => [
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]
                ])->toArray(),
                $branchId
            );

            // Redirect to Stripe
            return $paymentService->createCheckoutSession($order);

        } catch (\Exception $e) {
            session()->flash('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.checkout');
    }
}
