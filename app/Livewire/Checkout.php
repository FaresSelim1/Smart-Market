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
    
    // Contact & Shipping
    public $customerName = '';
    public $customerEmail = '';
    public $shippingAddress = '';
    
    public $couponError = '';
    public $couponMessage = '';

    public function mount(CartService $cartService)
    {
        if ($cartService->getCount() === 0) {
            return redirect()->route('cart');
        }

        if (Auth::check()) {
            $this->customerName = Auth::user()->name;
            $this->customerEmail = Auth::user()->email;
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
        $this->total = max(0, $this->subtotal - $this->discount);
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
     * Process the final order and redirect to Stripe.
     */
    public function processOrder(OrderService $orderService, PaymentService $paymentService)
    {
        $this->validate([
            'customerName'    => 'required|string|max:255',
            'customerEmail'   => 'required|email|max:255',
            'shippingAddress' => 'required|string|min:10',
        ], [
            'shippingAddress.required' => 'Please provide a delivery address.',
            'shippingAddress.min' => 'Address is too short.',
        ]);

        try {
            // 1. Create Pending Order
            $order = $orderService->createOrderFromCart(
                [
                    'name'    => $this->customerName,
                    'email'   => $this->customerEmail,
                    'address' => $this->shippingAddress,
                ],
                $this->discount,
                $this->couponCode
            );

            // 2. Create Stripe Session
            $session = $paymentService->createCheckoutSession($order);

            // 3. Save Stripe Session ID to Order
            $order->update(['stripe_session_id' => $session->id]);

            // 4. Redirect to Stripe Checkout
            return redirect($session->url);

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
