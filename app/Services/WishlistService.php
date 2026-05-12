<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class WishlistService
{
    public function toggle(int $productId): bool
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) return false;

        $exists = $user->wishlist()->where('product_id', $productId)->exists();

        if ($exists) {
            $user->wishlist()->detach($productId);
            return false;
        }

        $user->wishlist()->attach($productId);
        return true;
    }

    public function getWishlistItems(): Collection
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) return new Collection();

        return $user->wishlist()
            ->with(['category', 'images', 'primaryImage'])
            ->get();
    }
}