<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        if ($payload['type'] === 'checkout.session.completed') {
            $session = $payload['data']['object'];
            $orderId = $session['metadata']['order_id'];
            
            $order = Order::find($orderId);
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
            
            // Trigger queued job for invoice email
            \App\Jobs\SendOrderConfirmation::dispatch($order);
        }

        return response()->json(['status' => 'success']);
    }
}