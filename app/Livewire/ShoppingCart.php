<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Services\DiscountService;
use App\Services\PaymentService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

/**
 * Livewire component for the shopping cart page.
 * Handles cart display, coupon application, and checkout initiation.
 */
class ShoppingCart extends Component
{
    public string $couponCode = '';
    public float $discount = 0;
    public string $couponMessage = '';
    public string $couponError = '';

    protected $listeners = ['cart-updated' => '$refresh'];

    /**
     * Remove an item from the cart.
     */
    public function removeItem($productId): void
    {
        app(CartService::class)->removeItem((int) $productId);
        $this->discount = 0;
        $this->couponCode = '';
        $this->dispatch('cart-updated');
    }

    /**
     * Apply a coupon code to the cart.
     */
    public function applyCoupon(DiscountService $discountService): void
    {
        $this->couponMessage = '';
        $this->couponError = '';

        $subtotal = $this->getSubtotal();

        if (empty($this->couponCode)) {
            $this->couponError = 'Please enter a promo code.';
            return;
        }

        try {
            $result = $discountService->applyCoupon($this->couponCode, $subtotal);
            $this->discount = $result['discount'];
            $this->couponMessage = 'Promo applied! You saved $' . number_format($this->discount, 2);
        } catch (\Exception $e) {
            $this->discount = 0;
            $this->couponError = $e->getMessage();
        }
    }

    /**
     * Redirect to the dedicated checkout page.
     */
    public function checkout()
    {
        if (app(CartService::class)->getItems()->isEmpty()) {
            return;
        }

        return redirect()->route('checkout');
    }

    /**
     * Calculate cart subtotal from DB-backed cart items.
     */
    public function getSubtotal(): float
    {
        $items = app(CartService::class)->getItems();
        return $items->reduce(fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $items = app(CartService::class)->getItems();

        return view('livewire.shopping-cart', [
            'items'    => $items->keyBy('product_id')->toArray(),
            'subtotal' => $this->getSubtotal(),
            'total'    => max(0, $this->getSubtotal() - $this->discount),
        ]);
    }
}