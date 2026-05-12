@extends('layouts.app')

@section('content')
<div style="text-align: center; padding: 5rem 2rem;">
    <div style="background: #fef2f2; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; color: #dc2626;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width: 3rem; height: 3rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </div>
    
    <h1 style="font-weight: 900; font-size: 3rem; color: var(--brand-dark); margin-bottom: 1rem;">Payment Cancelled</h1>
    <p style="color: var(--text-muted); font-size: 1.25rem; max-width: 600px; margin: 0 auto 3rem;">
        The transaction protocol was aborted. No charges were made to your account.
    </p>
    
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="{{ route('checkout') }}" class="btn btn-yellow">Retry Checkout</a>
        <a href="/" class="btn" style="background: #f1f5f9; color: var(--brand-dark);">Return to Home</a>
    </div>
</div>
@endsection
