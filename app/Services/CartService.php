<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- Import this to fix the error
use Illuminate\Support\Collection;

/**
 * Service handling shopping cart persistence logic.
 * Follows the "Service Layer" graduation project requirement.
 */
class CartService
{
    /**
     * Add a product to the database-backed cart.
     * Uses updateOrCreate to handle existing items.
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $userId = Auth::id();
        if (!$userId) return;

        $product = Product::findOrFail($productId);

        // Check if item exists to handle quantity increment safely
        $cartItem = CartItem::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $cartItem->update(['price' => $product->current_price]); // Update to current price (with flash sale)
        } else {
            CartItem::create([
                'user_id'    => $userId,
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $product->current_price,
            ]);
        }
    }

    public function getItems(): Collection
    {
        $userId = Auth::id();
        if (!$userId) return collect();

        return CartItem::query()
            ->where('user_id', $userId)
            ->with('product')
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name'       => $item->product?->name,
                'quantity'   => (int) $item->quantity,
                'price'      => (float) $item->price,
            ]);
    }

    public function getCount(): int
    {
        $userId = Auth::id();
        return $userId ? (int) CartItem::where('user_id', $userId)->sum('quantity') : 0;
    }

    public function removeItem(int $productId): void
    {
        $userId = Auth::id();
        if ($userId) {
            CartItem::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
        }
    }

    public function clear(): void
    {
        $userId = Auth::id();
        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        }
    }
}