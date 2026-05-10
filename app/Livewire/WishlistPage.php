<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\WishlistService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

class WishlistPage extends Component
{
    #[On('wishlist-updated')]
    public function refresh() {}

    public function removeFromWishlist($productId, WishlistService $service)
    {
        $service->toggle($productId);
    }

    #[Layout('layouts.app')]
    public function render(WishlistService $service)
    {
        return view('livewire.wishlist-page', [
            'wishlist' => $service->getWishlistItems()
        ]);
    }
}