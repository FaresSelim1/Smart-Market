<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class StorefrontAddToCart extends Component
{
    public $productId;
    public $showIcon = false;
    public $buttonClass = 'btn btn-yellow px-6 py-3 font-bold w-full';

    public function mount($productId, $showIcon = false, $buttonClass = null)
    {
        $this->productId = $productId;
        $this->showIcon = $showIcon;
        if ($buttonClass) {
            $this->buttonClass = $buttonClass;
        }
    }

    public function addToCart(CartService $cart)
    {
        try {
            $cart->add((int)$this->productId, 1);
            $this->dispatch('cart-updated');
            session()->flash('message', 'Item successfully added to your cart!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AddToCart Failure: ' . $e->getMessage(), [
                'productId' => $this->productId,
                'exception' => $e
            ]);
            session()->flash('error', 'Could not add item to bag. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.storefront-add-to-cart');
    }
}

