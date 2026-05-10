<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class StorefrontAddToCart extends Component
{
    public int $productId;

    public function addToCart(CartService $cart)
    {
        if (!Auth::check()) {
            redirect()->guest(route('login'));
            return;
        }

        $cart->add($this->productId, 1);
        $this->dispatch('cart-updated');

        // keep UX simple; user stays on product page
        $this->dispatch('notify', message: 'Added to bag!');
    }

    public function render()
    {
        return view('livewire.storefront-add-to-cart');
    }
}

