<div class="wishlist-container">
    <style>
        .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2.5rem; }
        .wish-card {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 2rem;
            border: 1px solid #e2e8f0;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            box-shadow: var(--shadow-premium);
        }
        .wish-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 40px 80px -20px rgba(15, 23, 42, 0.15);
            border-color: var(--brand-yellow);
        }
        .wish-price { font-size: 1.5rem; font-weight: 900; color: var(--brand-dark); font-family: 'Outfit'; }
    </style>

    <div style="margin-bottom: 4rem;">
        <h1 style="font-weight: 900; color: var(--brand-dark); margin: 0; font-size: 2.5rem;">Hardware Vault</h1>
        <p style="color: var(--text-muted); margin: 0.5rem 0 0;">Items you've flagged for future deployment.</p>
    </div>

    <div class="wishlist-grid">
        @forelse($wishlist as $product)
            <div class="wish-card" wire:key="wish-{{ $product->id }}">
                <div style="margin-bottom: 1.5rem;">
                    <span style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">{{ $product->category->name ?? 'General' }}</span>
                    <h3 style="margin: 0.25rem 0 0; font-size: 1.3rem; font-weight: 800; color: var(--brand-dark);">{{ $product->name }}</h3>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                    <span class="wish-price">${{ number_format($product->price, 2) }}</span>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="{{ route('products.show', $product->id) }}" class="btn" style="background: #f1f5f9; color: var(--brand-dark); padding: 0.6rem 1rem; font-size: 0.8rem; text-decoration: none;">View</a>
                        <button wire:click="removeFromWishlist({{ $product->id }})"
                                class="btn" 
                                style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.6rem 1rem; font-size: 0.8rem; letter-spacing: 0;">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 8rem 0; background: rgba(255,255,255,0.4); border: 2px dashed #cbd5e1; border-radius: var(--radius-lg);">
                <div style="font-size: 4rem; margin-bottom: 2rem; opacity: 0.5;">🔒</div>
                <h2 style="font-weight: 900; color: var(--brand-dark); margin: 0 0 0.5rem;">The Vault is Empty</h2>
                <p style="color: var(--text-muted); margin-bottom: 2.5rem;">You haven't flagged any equipment for your vault yet.</p>
                <a href="/" class="btn btn-yellow">Browse Catalog</a>
            </div>
        @endforelse
    </div>
</div>