@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 4rem auto; text-align: center;">
    <div style="background: #fff; border-radius: 24px; padding: 3rem; border: 1px solid #e2e8f0;">
        <div style="width: 5rem; height: 5rem; background: #fef2f2; border-radius: 999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:2.5rem;height:2.5rem;color:#ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0 0 0.5rem;">Payment Cancelled</h1>
        <p style="color: var(--text-muted); margin: 0 0 2rem;">Your payment was not completed. Your cart items are still saved.</p>

        @if($order)
            <div style="background: #fef9c3; border-radius: 12px; padding: 1rem; margin-bottom: 2rem; font-size: 0.85rem; color: #854d0e;">
                Order <strong>{{ $order->order_number }}</strong> is pending payment. You can try again from your cart.
            </div>
        @endif

        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('cart') }}" class="btn btn-yellow" style="text-decoration:none; display:inline-block;">
                Return to Cart
            </a>
            <a href="{{ route('home') }}" class="btn" style="text-decoration:none; display:inline-block; background:#f1f5f9; color:var(--brand-dark);">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
