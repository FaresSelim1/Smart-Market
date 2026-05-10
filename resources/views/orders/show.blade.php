@extends('layouts.app')

@section('content')
@php
    $steps = ['pending', 'processing', 'shipped', 'delivered'];
    $currentIndex = array_search($order->status, $steps);
    if ($currentIndex === false) $currentIndex = 0;
    $progressPercent = $currentIndex > 0 ? ($currentIndex / (count($steps) - 1)) * 100 : 0;
@endphp

<div class="order-detail-container">
    <style>
        .detail-grid { display: grid; grid-template-columns: 1fr 380px; gap: 3rem; align-items: start; }
        .detail-card { background: #fff; border-radius: var(--radius-lg); padding: 3rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium); }
        .summary-card { 
            background: var(--brand-dark); 
            color: #fff; 
            border-radius: var(--radius-lg); 
            padding: 2.5rem; 
            position: sticky; 
            top: 110px;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.2);
        }
        .progress-track {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 3rem 0;
            padding: 0 1rem;
        }
        .progress-line {
            position: absolute;
            top: 12px;
            left: 5%;
            width: 90%;
            height: 4px;
            background: #f1f5f9;
            z-index: 1;
        }
        .progress-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: var(--brand-yellow);
            transition: width 0.8s cubic-bezier(0.65, 0, 0.35, 1);
        }
        .step-node {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .node-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #e2e8f0;
            transition: all 0.4s ease;
        }
        .node-active .node-circle {
            border-color: var(--brand-yellow);
            background: var(--brand-dark);
            box-shadow: 0 0 0 6px var(--brand-yellow-glow);
        }
        .step-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }
        .node-active .step-label { color: var(--brand-dark); }

        .item-list { margin-top: 3rem; }
        .item-entry {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .item-entry:last-child { border-bottom: none; }
    </style>

    <div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <a href="{{ route('orders.index') }}" style="text-decoration: none; color: var(--text-muted); font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                BACK TO LOGS
            </a>
            <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0; font-size: 2.5rem;">Deployment #{{ $order->order_number }}</h1>
        </div>
        <div style="text-align: right;">
            <span style="display: block; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Fulfillment Node</span>
            <span style="background: var(--brand-dark); color: var(--brand-yellow); padding: 8px 16px; border-radius: 12px; font-weight: 900; font-size: 0.8rem;">
                {{ $order->branch->name ?? 'Global Terminal' }}
            </span>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-weight: 900; font-size: 1.5rem;">Fulfillment Tracking</h2>
                <div style="text-align: right;">
                    <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Initiated: {{ $order->created_at->format('M d, Y • H:i') }}</span>
                </div>
            </div>

            <div class="progress-track">
                <div class="progress-line">
                    <div class="progress-fill" style="width: {{ $progressPercent }}%;"></div>
                </div>
                @foreach($steps as $index => $step)
                    <div class="step-node {{ $index <= $currentIndex ? 'node-active' : '' }}">
                        <div class="node-circle"></div>
                        <span class="step-label">{{ $step }}</span>
                    </div>
                @endforeach
            </div>

            <div class="item-list">
                <h3 style="font-weight: 900; font-size: 1.1rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid var(--brand-yellow); display: inline-block; padding-bottom: 4px;">Hardware Manifest</h3>
                @foreach($order->items as $item)
                    <div class="item-entry">
                        <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            🛡️
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="font-weight: 800; font-size: 1.05rem; color: var(--brand-dark);">{{ $item->product->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-top: 2px;">SKU: {{ $item->product->sku }}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 900; font-size: 1.1rem; color: var(--brand-dark);">${{ number_format($item->price * $item->quantity, 2) }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">{{ $item->quantity }} Units × ${{ number_format($item->price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="summary-card">
            <h3 style="margin: 0 0 2rem; font-weight: 900; color: var(--brand-yellow); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em;">Log Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; opacity: 0.7; font-size: 0.95rem;">
                <span>Gross Value</span>
                <span>${{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span>
            </div>

            @if($order->discount > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--brand-yellow); font-size: 0.95rem; font-weight: 800;">
                    <span>Credit Applied</span>
                    <span>-${{ number_format($order->discount, 2) }}</span>
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; margin-top: 2rem; font-size: 1.8rem; font-weight: 900; font-family: 'Outfit';">
                <span>Net Total</span>
                <span style="color: var(--brand-yellow);">${{ number_format($order->total_amount, 2) }}</span>
            </div>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.7rem; font-weight: 800; color: rgba(255,255,255,0.5); text-transform: uppercase; margin-bottom: 0.5rem;">Deployment Target</label>
                    <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; font-weight: 500;">{{ $order->shipping_address ?? 'Standard Deployment' }}</p>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label style="display: block; font-size: 0.7rem; font-weight: 800; color: rgba(255,255,255,0.5); text-transform: uppercase; margin-bottom: 0.5rem;">Protocol Status</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $order->payment_status === 'paid' ? '#4ade80' : '#facc15' }};"></span>
                            <span style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">{{ $order->payment_status ?? 'Awaiting' }}</span>
                        </div>
                    </div>
                    @if($order->coupon_code)
                        <div style="text-align: right;">
                            <label style="display: block; font-size: 0.7rem; font-weight: 800; color: rgba(255,255,255,0.5); text-transform: uppercase; margin-bottom: 0.5rem;">Auth Code</label>
                            <span style="background: rgba(250, 204, 21, 0.1); color: var(--brand-yellow); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 900; border: 1px solid rgba(250, 204, 21, 0.2);">{{ $order->coupon_code }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('orders.invoice', $order) }}"
               class="btn btn-yellow" 
               style="width: 100%; margin-top: 3rem; padding: 1.1rem; font-size: 0.95rem; text-transform: uppercase; font-weight: 900; text-decoration: none; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Manifest (PDF)
            </a>
        </div>
    </div>
</div>
@endsection