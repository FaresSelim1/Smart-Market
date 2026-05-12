<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Order model representing a customer purchase.
 * Scoped to a branch and tracks payment + fulfillment status.
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Valid order status transitions.
     * Key = current status, Value = array of allowed next statuses.
     */
    public const STATUS_TRANSITIONS = [
        'pending'    => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['delivered'],
        'delivered'  => [],
        'cancelled'  => [],
    ];

    protected $fillable = [
        'user_id',
        'branch_id',
        'order_number',
        'total_amount',
        'discount',
        'coupon_code',
        'status',
        'payment_status',
        'shipping_address',
        'stripe_session_id',
        'stripe_payment_intent',
        'customer_name',
        'customer_email',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount'     => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if a status transition is valid.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = self::STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array($newStatus, $allowed);
    }

    /**
     * Transition to a new status with validation.
     *
     * @throws \InvalidArgumentException
     */
    public function transitionTo(string $newStatus): void
    {
        if (! $this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from '{$this->status}' to '{$newStatus}'."
            );
        }

        $this->update(['status' => $newStatus]);
    }
}