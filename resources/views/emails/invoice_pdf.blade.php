<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .invoice-header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #0f172a; padding-bottom: 20px; }
        .invoice-header h1 { color: #0f172a; margin: 0; text-transform: uppercase; }
        .details { display: table; width: 100%; margin-bottom: 30px; }
        .details-col { display: table-cell; width: 50%; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f1f5f9; text-align: left; padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .total-section { text-align: right; }
        .total-row { font-size: 16px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; font-size: 10px; color: #64748b; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>SmartMarket Invoice</h1>
        <p>Order #{{ $order->order_number }}</p>
    </div>

    <div class="details">
        <div class="details-col">
            <strong>Billed To:</strong><br>
            {{ $order->user->name }}<br>
            {{ $order->user->email }}<br>
            <br>
            <strong>Shipping Address:</strong><br>
            {{ $order->shipping_address }}
        </div>
        <div class="details-col" style="text-align: right;">
            <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
            <strong>Status:</strong> {{ ucfirst($order->status) }}<br>
            <strong>Branch:</strong> {{ $order->branch->name }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p>Subtotal: ${{ number_format($order->total_amount + $order->discount, 2) }}</p>
        @if($order->discount > 0)
            <p style="color: #22c55e;">Discount: -${{ number_format($order->discount, 2) }}</p>
        @endif
        <div class="total-row">
            Total Paid: ${{ number_format($order->total_amount, 2) }}
        </div>
    </div>

    <div class="footer">
        Thank you for choosing SmartMarket. This is a computer-generated invoice.
    </div>
</body>
</html>