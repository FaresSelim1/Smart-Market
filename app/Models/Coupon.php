<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Coupon model for the discount/promo system.
 * Supports fixed and percentage discounts with expiry, usage limits,
 * minimum cart value, and active/inactive toggling.
 */
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',        // 'fixed' or 'percent'
        'value',
        'max_uses',
        'used_count',
        'min_cart_value',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at'     => 'datetime',
        'value'          => 'decimal:2',
        'min_cart_value'  => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    /**
     * Check if the coupon is still valid for use.
     * Handles nullable expires_at and max_uses gracefully.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if the coupon meets the minimum cart value requirement.
     */
    public function meetsMinCartValue(float $cartTotal): bool
    {
        if ($this->min_cart_value === null || $this->min_cart_value <= 0) {
            return true;
        }

        return $cartTotal >= (float) $this->min_cart_value;
    }

    /**
     * Calculate the discount amount for the given cart total.
     */
    public function calculateDiscount(float $cartTotal): float
    {
        if ($this->type === 'percent') {
            return round($cartTotal * ($this->value / 100), 2);
        }

        return min((float) $this->value, $cartTotal);
    }
}