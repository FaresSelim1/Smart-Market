<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;

/**
 * Service handling Stripe payment integration.
 * Creates checkout sessions and processes payments.
 */
class PaymentService
{
    private bool $stripeEnabled;

    public function __construct()
    {
        $secret = config('services.stripe.secret');
        $this->stripeEnabled = !empty($secret);

        if ($this->stripeEnabled) {
            Stripe::setApiKey($secret);
        }
    }

    /**
     * Create a Stripe Checkout Session for the given order.
     *
     * @param \App\Models\Order $order
     * @return Session|object  Returns a Stripe Session or a mock object for dev
     */
    public function createCheckoutSession($order)
    {
        if (! $this->stripeEnabled) {
            // Fallback for development without Stripe keys
            // Returns a mock object that redirects to payment success
            return (object) [
                'url' => route('payment.success', ['order_ref' => $order->order_number]),
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => 'Order ' . $order->order_number],
                    'unit_amount'  => (int) ($order->total_amount * 100), // Amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('payment.success') . '?order_ref=' . $order->order_number . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.cancel') . '?order_ref=' . $order->order_number,
            'metadata'    => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);
    }

    /**
     * Check if Stripe is properly configured.
     */
    public function isEnabled(): bool
    {
        return $this->stripeEnabled;
    }
}