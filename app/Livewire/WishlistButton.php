<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\WishlistService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WishlistButton extends Component
{
    public $productId;
    public $isWishlisted;

    public function mount($productId)
    {
        $this->productId = $productId;
        
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->isWishlisted = $user->wishlist()
                                       ->where('product_id', $productId)
                                       ->exists(); // Fixed typo here
        } else {
            $this->isWishlisted = false;
        }
    }

    public function toggleWishlist(WishlistService $service)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->isWishlisted = $service->toggle($this->productId);
        $this->dispatch('wishlist-updated'); 
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}