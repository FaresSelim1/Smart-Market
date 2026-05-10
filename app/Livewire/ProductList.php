<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Services\CartService;

class ProductList extends Component
{
    public $search = '';

    public function addToCart($productId, CartService $cart)
    {
        $cart->add($productId);
        $this->dispatch('cart-updated'); // Emit event for navbar update
    }

    public function render()
    {
        $products = Product::where('name', 'like', "%{$this->search}%")->get();
        return view('livewire.product-list', compact('products'));
    }
}