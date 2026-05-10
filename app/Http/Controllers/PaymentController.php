<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Jobs\SendOrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller handling payment gateway callbacks (success/cancel/webhook).
 */
class PaymentController extends Controller
{
    /**
     * Handle the Success Callback from Stripe/PayMob.
     */
    public function handleGatewayCallback(Request $request)
    {
        // Retrieve order by order_number, ensuring the authenticated user owns it
        $order = Order::where('order_number', $request->order_ref)
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

        // Update status as per project criteria
        $order->update([
            'payment_status' => 'paid',
            'status'         => 'processing',
        ]);

        // Trigger the Queued Job for Email & PDF Invoice
        SendOrderConfirmation::dispatch($order);

        // Clear the DB-backed cart after confirmed payment success
        app(CartService::class)->clear();

        logger()->info('Cart cleared after payment success', [
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'user_id'      => $order->user_id,
        ]);

        return view('checkout.success', compact('order'));
    }

    /**
     * Handle the Cancel Callback from payment gateway.
     */
    public function handleCancel(Request $request)
    {
        $order = null;

        if ($request->has('order_ref')) {
            $order = Order::where('order_number', $request->order_ref)
                         ->where('user_id', Auth::id())
                         ->first();
        }

        return view('checkout.cancel', compact('order'));
    }

    /**
     * Webhook for asynchronous server-to-server confirmation.
     */
    public function webhook(Request $request)
    {
        // Basic webhook handling — signature verification should be added
        // for production Stripe integration
        $payload = $request->all();

        if (($payload['type'] ?? '') === 'checkout.session.completed') {
            $session = $payload['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status'         => 'processing',
                    ]);

                    SendOrderConfirmation::dispatch($order);
                }
            }
        }

        return response()->json(['status' => 'verified']);
    }
}