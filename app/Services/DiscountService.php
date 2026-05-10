<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;

/**
 * Service handling coupon/discount logic.
 * Validates coupon rules including expiry, usage limits, min cart value,
 * and applies the discount correctly.
 */
class DiscountService
{
    /**
     * Apply a coupon code to the current cart total.
     *
     * @param string $code       The coupon code entered by the user
     * @param float  $cartTotal  The current subtotal of the cart
     * @return array             ['total' => float, 'discount' => float, 'coupon' => Coupon]
     *
     * @throws \Exception If the coupon is invalid, expired, or doesn't meet requirements
     */
    public function applyCoupon(string $code, float $cartTotal): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            throw new \Exception('Coupon code not found.');
        }

        if (! $coupon->isValid()) {
            throw new \Exception('This coupon is expired or has reached its usage limit.');
        }

        if (! $coupon->meetsMinCartValue($cartTotal)) {
            throw new \Exception(
                'Minimum cart value of $' . number_format($coupon->min_cart_value, 2) . ' required.'
            );
        }

        $discount = $coupon->calculateDiscount($cartTotal);
        $newTotal = max(0, $cartTotal - $discount);

        // Increment the usage count
        $coupon->increment('used_count');

        return [
            'total'    => $newTotal,
            'discount' => $discount,
            'coupon'   => $coupon,
        ];
    }
}