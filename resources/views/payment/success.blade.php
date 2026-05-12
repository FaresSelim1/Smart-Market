@extends('layouts.app')

@section('content')
<div style="text-align: center; padding: 5rem 2rem;">
    <div style="background: #ecfdf5; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; color: #059669;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 3rem; height: 3rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>
    
    <h1 style="font-weight: 900; font-size: 3rem; color: var(--brand-dark); margin-bottom: 1rem;">Payment Successful!</h1>
    <p style="color: var(--text-muted); font-size: 1.25rem; max-width: 600px; margin: 0 auto 3rem;">
        Your equipment deployment has been authorized. Order <strong>{{ $order->order_number }}</strong> is now being processed.
    </p>
    
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="{{ route('orders.index') }}" class="btn btn-yellow">View My Orders</a>
        <a href="/" class="btn" style="background: #f1f5f9; color: var(--brand-dark);">Continue Shopping</a>
    </div>
</div>
@endsection
