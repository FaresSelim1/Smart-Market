@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 4rem auto; text-align: center;">
    <div style="background: #fff; border-radius: 24px; padding: 3rem; border: 1px solid #e2e8f0;">
        <div style="width: 5rem; height: 5rem; background: #dcfce7; border-radius: 999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:2.5rem;height:2.5rem;color:#22c55e;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0 0 0.5rem;">Payment Successful!</h1>
        <p style="color: var(--text-muted); margin: 0 0 2rem;">Your order has been confirmed and is now being processed.</p>

        <div style="background: #f8fafc; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; text-align: left;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted); font-size: 0.85rem;">Order Number</span>
                <span style="font-weight: 800; color: var(--brand-dark);">{{ $order->order_number }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted); font-size: 0.85rem;">Total Paid</span>
                <span style="font-weight: 800; color: var(--brand-dark);">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted); font-size: 0.85rem;">Status</span>
                <span style="font-weight: 800; color: #22c55e;">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-yellow" style="text-decoration:none; display:inline-block;">
                View Order Details
            </a>
            <a href="{{ route('home') }}" class="btn" style="text-decoration:none; display:inline-block; background:#f1f5f9; color:var(--brand-dark);">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
