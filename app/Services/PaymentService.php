<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for the given order.
     */
    public function createCheckoutSession(Order $order): Session
    {
        $lineItems = $order->orderItems->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => (int) ($item->price * 100), // Stripe expects amounts in cents
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        // Add discount as a separate line item if present
        if ($order->discount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Discount (' . ($order->coupon_code ?? 'Promo') . ')',
                    ],
                    'unit_amount' => -(int) ($order->discount * 100),
                ],
                'quantity' => 1,
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'client_reference_id' => $order->id,
            'customer_email' => $order->user?->email ?? $order->customer_email,
        ]);
    }

    /**
     * Retrieve a Stripe Session by ID.
     */
    public function getSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }
}