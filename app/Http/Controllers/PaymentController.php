<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $cartService;

    public function __construct(PaymentService $paymentService, CartService $cartService)
    {
        $this->paymentService = $paymentService;
        $this->cartService = $cartService;
    }

    /**
     * Handle Stripe Success Callback.
     */
    public function handleGatewayCallback(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('home');
        }

        try {
            $session = $this->paymentService->getSession($sessionId);
            $orderId = $session->client_reference_id;
            $order = Order::findOrFail($orderId);

            if ($session->payment_status === 'paid') {
                $this->finalizeOrder($order, $session->payment_intent);
                $this->cartService->clear();
                
                return view('payment.success', ['order' => $order]);
            }

            return redirect()->route('payment.cancel');

        } catch (\Exception $e) {
            Log::error('Stripe Success Callback Error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Something went wrong while verifying your payment.');
        }
    }

    /**
     * Handle Stripe Cancel Callback.
     */
    public function handleCancel(Request $request)
    {
        return view('payment.cancel');
    }

    /**
     * Stripe Webhook Handler.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $orderId = $session->client_reference_id;
                $order = Order::find($orderId);
                if ($order && $order->payment_status !== 'paid') {
                    $this->finalizeOrder($order, $session->payment_intent);
                }
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $order = Order::where('stripe_payment_intent', $intent->id)->first();
                if ($order) {
                    $order->update(['status' => 'failed', 'payment_status' => 'failed']);
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Finalize the order: update status and reduce stock.
     */
    protected function finalizeOrder(Order $order, string $paymentIntentId)
    {
        if ($order->payment_status === 'paid') return;

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $paymentIntentId) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'stripe_payment_intent' => $paymentIntentId,
            ]);

            // Reduce stock from the specific branch assigned to the order
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                $branch = $product->branches()->where('branches.id', $order->branch_id)->first();
                
                if ($branch && $branch->pivot->stock_level >= $item->quantity) {
                    $branch->pivot->decrement('stock_level', $item->quantity);
                } else {
                    Log::warning("Stock discrepancy during finalization for Order {$order->id}, Product {$product->id}, Branch {$order->branch_id}");
                }
            }
        });
    }
}