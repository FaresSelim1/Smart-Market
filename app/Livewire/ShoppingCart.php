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
     * Increment item quantity.
     */
    public function incrementQuantity($productId): void
    {
        $items = app(CartService::class)->getItems();
        $item = $items->firstWhere('product_id', $productId);
        
        if ($item) {
            app(CartService::class)->updateQuantity((int) $productId, $item['quantity'] + 1);
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Decrement item quantity.
     */
    public function decrementQuantity($productId): void
    {
        $items = app(CartService::class)->getItems();
        $item = $items->firstWhere('product_id', $productId);
        
        if ($item) {
            $newQty = $item['quantity'] - 1;
            if ($newQty <= 0) {
                $this->removeItem($productId);
            } else {
                app(CartService::class)->updateQuantity((int) $productId, $newQty);
                $this->dispatch('cart-updated');
            }
        }
    }

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