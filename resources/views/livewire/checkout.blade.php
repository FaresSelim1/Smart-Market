<div class="checkout-container">
    <style>
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 3rem; align-items: start; }
        .checkout-card { background: #fff; border-radius: var(--radius-lg); padding: 3rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-premium); }
        .summary-card { 
            background: var(--brand-dark); 
            color: #fff; 
            border-radius: var(--radius-lg); 
            padding: 2.5rem; 
            position: sticky; 
            top: 110px;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.2);
        }
        .form-group { margin-bottom: 2rem; }
        .form-label { display: block; font-weight: 800; color: var(--brand-dark); margin-bottom: 0.75rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control { width: 100%; padding: 1.1rem; border: 1px solid #e2e8f0; border-radius: 14px; font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: all 0.3s ease; }
        .form-control:focus { outline: none; border-color: var(--brand-yellow); box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.1); transform: translateY(-2px); }
        .order-item { display: flex; justify-content: space-between; padding: 1.25rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .order-item:last-child { border-bottom: none; }
    </style>

    <div style="margin-bottom: 3rem;">
        <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0 0 0.5rem;">Secure Deployment</h1>
        <p style="color: var(--text-muted); margin: 0;">Confirm your details to finalize equipment deployment.</p>
    </div>

    <div class="checkout-grid">
        <div class="checkout-card">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem;">
                <div style="background: var(--brand-yellow); color: var(--brand-dark); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-family: 'Outfit';">1</div>
                <h2 style="margin: 0; font-weight: 900; font-size: 1.5rem;">Deployment Target</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Recipient Name</label>
                    <input type="text" wire:model="customerName" class="form-control" placeholder="Full name">
                    @error('customerName') <span style="color:#ef4444; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Authorization Email</label>
                    <input type="email" wire:model="customerEmail" class="form-control" placeholder="email@example.com">
                    @error('customerEmail') <span style="color:#ef4444; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deployment Coordinates (Address)</label>
                <textarea wire:model="shippingAddress" 
                          placeholder="Street address, building, unit, city..."
                          class="form-control @error('shippingAddress') is-invalid @enderror" 
                          style="min-height: 120px; resize: none;"></textarea>
                @error('shippingAddress')
                    <span style="color: #ef4444; font-size: 0.85rem; font-weight: 700; margin-top: 0.75rem; display: flex; align-items: center; gap: 4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                    <div style="background: var(--brand-yellow); color: var(--brand-dark); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-family: 'Outfit';">2</div>
                    <h2 style="margin: 0; font-weight: 900; font-size: 1.5rem;">Transaction Protocol</h2>
                </div>
                
                <div style="padding: 2rem; border: 2px solid var(--brand-yellow); background: rgba(250, 204, 21, 0.05); border-radius: var(--radius-md); display: flex; align-items: center; gap: 1.5rem;">
                    <div style="background: var(--brand-yellow); color: var(--brand-dark); padding: 1rem; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(250, 204, 21, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 2rem; height: 2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 900; font-size: 1.25rem; color: var(--brand-dark); font-family: 'Outfit';">Stripe Secure Protocol</div>
                        <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">PCI-DSS Compliant Encryption • 256-bit SSL</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="summary-card">
            <h3 style="margin: 0 0 2rem; font-weight: 900; color: var(--brand-yellow); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.1em;">Final Manifest</h3>
            
            <div style="margin-bottom: 2.5rem;">
                @foreach($items as $item)
                    <div class="order-item">
                        <div style="flex: 1;">
                            <div style="font-weight: 800; font-size: 0.95rem; line-height: 1.2;">{{ $item['name'] }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.6; margin-top: 0.25rem;">X {{ $item['quantity'] }} UNITS</div>
                        </div>
                        <div style="font-weight: 900; font-family: 'Outfit'; font-size: 1.1rem;">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; opacity: 0.7; font-size: 0.95rem;">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>

            @if($discount > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--brand-yellow); font-size: 0.95rem; font-weight: 800;">
                    <span>Discount</span>
                    <span>-${{ number_format($discount, 2) }}</span>
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; margin-top: 2rem; font-size: 1.8rem; font-weight: 900; font-family: 'Outfit';">
                <span>Total</span>
                <span style="color: var(--brand-yellow);">${{ number_format($total, 2) }}</span>
            </div>

            <button wire:click="processOrder" 
                    wire:loading.attr="disabled"
                    class="btn btn-yellow" 
                    style="width: 100%; margin-top: 3rem; padding: 1.3rem; font-size: 1.1rem; text-transform: uppercase; font-weight: 900; letter-spacing: 0.1em;">
                <span wire:loading.remove>Initialize Payment</span>
                <span wire:loading>Authenticating...</span>
            </button>

            @if(session()->has('error'))
                <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; font-size: 0.85rem; color: #f87171; font-weight: 700; text-align: center; animation: shake 0.4s ease-in-out;">
                    {{ session('error') }}
                </div>
                <style>@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }</style>
            @endif
        </div>
    </div>
</div>
