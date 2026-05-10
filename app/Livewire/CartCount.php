<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(CartService $cart)
    {
        $this->count = $cart->getCount();
    }

    #[On('cart-updated')]
    public function updateCount(CartService $cart): void
    {
        $this->count = $cart->getCount();
    }

    public function render()
    {
        return view('livewire.cart-count', [
            'count' => $this->count,
        ]);
    }
}

