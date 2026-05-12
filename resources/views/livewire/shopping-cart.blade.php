<div class="cart-wrapper">
    <style>
        .cart-grid { display: grid; grid-template-columns: 1fr 400px; gap: 3rem; align-items: start; }
        .cart-main { background: #fff; border-radius: var(--radius-lg); padding: 3rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium); }
        .summary-side { 
            background: var(--brand-dark); 
            color: #fff; 
            border-radius: var(--radius-lg); 
            padding: 2.5rem; 
            position: sticky; 
            top: 110px;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.2);
        }
        .item-row { 
            display: flex; 
            gap: 1.5rem;
            padding: 2rem 0; 
            border-bottom: 1px solid #f1f5f9; 
            transition: opacity 0.3s ease;
        }
        .item-row:last-child { border-bottom: none; }
        .item-image { width: 100px; height: 100px; background: #f8fafc; border-radius: 16px; object-fit: cover; }
        .btn-checkout { width: 100%; margin-top: 2rem; padding: 1.2rem; font-size: 1.1rem; }
        
        .promo-input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            color: #fff;
            font-family: inherit;
        }
        @media (max-width: 1024px) {
            .cart-grid { grid-template-columns: 1fr; }
            .summary-side { position: static; }
        }
    </style>

    <div style="margin-bottom: 3rem;">
        <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0;">Shopping Bag</h1>
        <p style="color: var(--text-muted); margin: 0.5rem 0 0;">Review your equipment before proceeding to checkout.</p>
    </div>

    <div class="cart-grid">
        <div class="cart-main">
            @forelse($items as $id => $item)
                <div class="item-row" wire:key="item-{{ $id }}">
                    <a href="{{ route('products.show', $item['slug'] ?? $id) }}" class="item-link" style="flex-shrink: 0;">
                        @if(isset($item['image']))
                            <img src="{{ asset('storage/' . $item['image']) }}" class="item-image" alt="{{ $item['name'] }}">
                        @else
                            <div class="item-image" style="display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #f1f5f9;">
                                📦
                            </div>
                        @endif
                    </a>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <a href="{{ route('products.show', $item['slug'] ?? $id) }}" style="text-decoration: none;">
                                    <div style="font-weight: 800; font-size: 1.1rem; color: var(--brand-dark);">{{ $item['name'] }}</div>
                                </a>
                                <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.75rem;">
                                    <div style="display: flex; align-items: center; background: #f1f5f9; border-radius: 8px; padding: 4px;">
                                        <button wire:click="decrementQuantity({{ $id }})" 
                                                wire:loading.attr="disabled"
                                                style="border: none; background: none; padding: 4px 10px; cursor: pointer; font-weight: 900; color: var(--brand-dark);">-</button>
                                        <span style="font-weight: 800; min-width: 20px; text-align: center; font-size: 0.9rem;">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementQuantity({{ $id }})" 
                                                wire:loading.attr="disabled"
                                                style="border: none; background: none; padding: 4px 10px; cursor: pointer; font-weight: 900; color: var(--brand-dark);">+</button>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 900; font-size: 1.1rem; color: var(--brand-dark);">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">${{ number_format($item['price'], 2) }} each</div>
                            </div>
                        </div>
                        <button wire:click="removeItem({{ $id }})" 
                                wire:loading.attr="disabled"
                                style="margin-top: 1rem; color: #ef4444; border: none; background: none; cursor: pointer; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.05em; padding: 0; display: flex; align-items: center; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            REMOVE ITEM
                        </button>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem 0;">
                    <div style="font-size: 4rem; margin-bottom: 1.5rem;">🛒</div>
                    <h2 style="color: var(--brand-dark); font-weight: 800;">Your bag is empty</h2>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">Looks like you haven't added any equipment yet.</p>
                    <a href="/" class="btn btn-yellow">Back to Catalog</a>
                </div>
            @endforelse
        </div>

        <div class="summary-side">
            <h3 style="margin: 0 0 2rem; font-weight: 900; color: var(--brand-yellow); font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.1em;">Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; opacity: 0.7; font-size: 0.95rem;">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>

            @if($discount > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--brand-yellow); font-size: 0.95rem; font-weight: 700;">
                    <span>Discount</span>
                    <span>-${{ number_format($discount, 2) }}</span>
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; margin-top: 2rem; font-size: 1.75rem; font-weight: 900;">
                <span>Total</span>
                <span style="color: var(--brand-yellow);">${{ number_format($total, 2) }}</span>
            </div>

            <div style="margin-top: 3rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(255,255,255,0.5); margin-bottom: 0.75rem; text-transform: uppercase;">Promotional Code</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" wire:model="couponCode" placeholder="Enter code" class="promo-input">
                    <button wire:click="applyCoupon" wire:loading.attr="disabled" class="btn btn-yellow" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">Apply</button>
                </div>
                
                @if($couponMessage)
                    <div style="margin-top: 0.75rem; font-size: 0.8rem; color: #4ade80; font-weight: 700;">{{ $couponMessage }}</div>
                @endif
                @if($couponError)
                    <div style="margin-top: 0.75rem; font-size: 0.8rem; color: #f87171; font-weight: 700;">{{ $couponError }}</div>
                @endif
            </div>

            <button wire:click="checkout"
                    wire:loading.attr="disabled"
                    class="btn btn-yellow btn-checkout"
                    {{ count($items) === 0 ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="checkout">Proceed to Checkout</span>
                <span wire:loading wire:target="checkout">Securing Session...</span>
            </button>

            <div style="margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0.5; font-size: 0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Secure 256-bit SSL Encryption
            </div>
        </div>
    </div>
</div>