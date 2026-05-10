<div style="font-family: 'Segoe UI', sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0f172a; color: #fff; padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
        <h1 style="margin: 0; color: #facc15; font-size: 24px;">GO.MARKET</h1>
        <p style="margin: 10px 0 0; opacity: 0.7; font-size: 14px;">Order Confirmation</p>
    </div>

    <div style="background: #fff; padding: 30px; border: 1px solid #e2e8f0; border-top: none;">
        <h2 style="color: #0f172a; margin: 0 0 5px;">Thank you for your order!</h2>
        <p style="color: #64748b; margin: 0 0 20px;">Your order has been received and is being processed.</p>

        <div style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #64748b;">Order Number:</td>
                    <td style="padding: 4px 0; font-weight: 700; text-align: right;">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;">Branch:</td>
                    <td style="padding: 4px 0; font-weight: 700; text-align: right;">{{ $order->branch->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;">Status:</td>
                    <td style="padding: 4px 0; font-weight: 700; text-align: right; color: #22c55e;">{{ ucfirst($order->status) }}</td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0;">
                    <th style="text-align: left; padding: 10px 0; color: #64748b; font-size: 12px; text-transform: uppercase;">Product</th>
                    <th style="text-align: center; padding: 10px 0; color: #64748b; font-size: 12px; text-transform: uppercase;">Qty</th>
                    <th style="text-align: right; padding: 10px 0; color: #64748b; font-size: 12px; text-transform: uppercase;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px 0;">{{ $item->product->name }}</td>
                    <td style="padding: 10px 0; text-align: center;">{{ $item->quantity }}</td>
                    <td style="padding: 10px 0; text-align: right;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #0f172a; text-align: right;">
            @if($order->discount > 0)
                <p style="margin: 0; color: #22c55e; font-size: 14px;">Discount: -${{ number_format($order->discount, 2) }}</p>
            @endif
            <p style="margin: 5px 0 0; font-size: 20px; font-weight: 900; color: #0f172a;">
                Total: ${{ number_format($order->total_amount, 2) }}
            </p>
        </div>
    </div>

    <div style="background: #f8fafc; padding: 20px; border-radius: 0 0 16px 16px; border: 1px solid #e2e8f0; border-top: none; text-align: center;">
        <p style="margin: 0; color: #64748b; font-size: 12px;">
            Your invoice is attached to this email as a PDF.<br>
            Thank you for shopping with GO.MARKET!
        </p>
    </div>
</div>
