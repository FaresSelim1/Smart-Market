@extends('layouts.app')

@section('content')
<div class="orders-container">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4rem;
        }
        .order-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid #e2e8f0;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: var(--shadow-premium);
        }
        .order-card:hover {
            transform: scale(1.01) translateY(-4px);
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.1);
            border-color: var(--brand-yellow);
        }
        .status-badge {
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fef2f2; color: #991b1b; }
        
        .order-icon {
            width: 60px;
            height: 60px;
            background: var(--brand-dark);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-yellow);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }
    </style>

    <div class="page-header">
        <div>
            <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0; font-size: 2.5rem;">Deployment Logs</h1>
            <p style="color: var(--text-muted); margin: 0.5rem 0 0;">Historical record of equipment fulfillment and branch deliveries.</p>
        </div>
        <div style="background: var(--bg-glass); backdrop-filter: blur(8px); padding: 0.75rem 1.25rem; border-radius: 14px; border: 1px solid var(--glass-border); display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Active Node:</span>
            <span style="font-size: 0.75rem; font-weight: 900; color: var(--brand-dark);">{{ session('active_branch_name', 'Global Terminal') }}</span>
        </div>
    </div>

    @forelse($orders as $order)
        <div class="order-card">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 2rem;">
                <div style="display: flex; align-items: center; gap: 2rem;">
                    <div class="order-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                            <h3 style="margin: 0; font-weight: 800; font-size: 1.25rem; color: var(--brand-dark);">{{ $order->order_number }}</h3>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                            <span>Logged: {{ $order->created_at->format('M d, Y') }}</span>
                            <span style="opacity: 0.3;">|</span>
                            <span>{{ $order->items_count ?? $order->items()->count() }} units attached</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 3rem;">
                    <div style="text-align: right;">
                        <div style="font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem;">Total Value</div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: var(--brand-dark); font-family: 'Outfit';">${{ number_format($order->total_amount, 2) }}</div>
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-yellow" style="padding: 0.75rem 1.5rem; font-size: 0.85rem; text-decoration: none;">
                        Details
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 8rem 0; background: rgba(255,255,255,0.4); border: 2px dashed #cbd5e1; border-radius: var(--radius-lg);">
            <div style="font-size: 4rem; margin-bottom: 2rem; opacity: 0.5;">📋</div>
            <h2 style="font-weight: 900; color: var(--brand-dark); margin: 0 0 0.5rem;">No Deployment Records</h2>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem;">You haven't initiated any equipment deployments yet.</p>
            <a href="/" class="btn btn-yellow">Access Equipment Catalog</a>
        </div>
    @endforelse

    @if($orders->hasPages())
        <div style="margin-top: 3rem; display: flex; justify-content: center;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection