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
    private const SESSION_KEY = 'guest_cart';

    /**
     * Add a product to the cart (DB for users, session for guests).
     */
    public function add(int $productId, int $quantity = 1): void
    {
        \Illuminate\Support\Facades\Log::info("Attempting to add to cart", ['productId' => $productId, 'quantity' => $quantity]);
        $product = Product::findOrFail($productId);
        $userId = Auth::id();

        if ($userId) {
            // Logged-in user: Database storage
            $cartItem = CartItem::where('user_id', $userId)
                                ->where('product_id', $productId)
                                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
                $cartItem->update(['price' => $product->current_price]);
            } else {
                CartItem::create([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $product->current_price,
                ]);
            }
        } else {
            // Guest user: Session storage
            $cart = session()->get(self::SESSION_KEY, []);

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
                $cart[$productId]['price'] = (float) $product->current_price;
            } else {
                $cart[$productId] = [
                    'product_id' => $productId,
                    'name'       => $product->name,
                    'quantity'   => $quantity,
                    'price'      => (float) $product->current_price,
                ];
            }

            session()->put(self::SESSION_KEY, $cart);
        }
    }

    /**
     * Get all items in the cart with full product details.
     */
    public function getItems(): Collection
    {
        $userId = Auth::id();

        if ($userId) {
            return CartItem::query()
                ->where('user_id', $userId)
                ->with(['product.primaryImage'])
                ->get()
                ->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'name'       => $item->product?->name,
                    'slug'       => $item->product?->id, // Fallback to ID if slug missing
                    'quantity'   => (int) $item->quantity,
                    'price'      => (float) $item->price,
                    'image'      => $item->product?->primaryImage?->path,
                ]);
        }

        $cart = session()->get(self::SESSION_KEY, []);
        $items = collect($cart);

        // Enhance guest cart items with fresh product data (images/slugs)
        return $items->map(function($item) {
            $product = Product::with('primaryImage')->find($item['product_id']);
            return array_merge($item, [
                'image' => $product?->primaryImage?->path,
                'slug'  => $product?->id,
            ]);
        });
    }

    /**
     * Update quantity of a specific product.
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $product = Product::findOrFail($productId);
        $userId = Auth::id();

        if ($userId) {
            CartItem::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->update([
                        'quantity' => $quantity,
                        'price'    => $product->current_price
                    ]);
        } else {
            $cart = session()->get(self::SESSION_KEY, []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
                $cart[$productId]['price'] = (float) $product->current_price;
                session()->put(self::SESSION_KEY, $cart);
            }
        }
    }

    /**
     * Get total item count (sum of quantities).
     */
    public function getCount(): int
    {
        $userId = Auth::id();

        if ($userId) {
            return (int) CartItem::where('user_id', $userId)->sum('quantity');
        }

        $cart = session()->get(self::SESSION_KEY, []);
        return (int) collect($cart)->sum('quantity');
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $productId): void
    {
        $userId = Auth::id();

        if ($userId) {
            CartItem::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
        } else {
            $cart = session()->get(self::SESSION_KEY, []);
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                session()->put(self::SESSION_KEY, $cart);
            }
        }
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        $userId = Auth::id();

        if ($userId) {
            CartItem::where('user_id', $userId)->delete();
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }

    /**
     * Merge guest cart into user cart after login/registration.
     */
    public function merge(): void
    {
        $userId = Auth::id();
        if (!$userId) return;

        $guestCart = session()->get(self::SESSION_KEY, []);
        if (empty($guestCart)) return;

        foreach ($guestCart as $productId => $details) {
            $this->add($productId, $details['quantity']);
        }

        session()->forget(self::SESSION_KEY);
    }
}