<div class="storefront-container">
    <style>
        .hero-section {
            background: var(--brand-dark);
            border-radius: var(--radius-lg);
            padding: 5rem 4rem;
            margin-bottom: 4rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.3);
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--brand-yellow) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(80px);
        }
        .hero-title { font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; }
        .hero-subtitle { font-size: 1.25rem; color: #94a3b8; max-width: 600px; }

        .search-container {
            background: #fff;
            padding: 1rem;
            border-radius: 20px;
            display: flex;
            gap: 1rem;
            margin-top: -3.5rem;
            margin-left: 2rem;
            margin-right: 2rem;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }

        .filter-select {
            padding: 1rem 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            color: var(--brand-dark);
            background: #f8fafc;
            min-width: 200px;
        }

        .search-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
        }

        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 2.5rem; 
            margin-top: 5rem; 
        }

        .product-card { 
            background: #fff; 
            border-radius: var(--radius-lg); 
            padding: 1.5rem; 
            border: 1px solid #e2e8f0; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative; 
            display: flex;
            flex-direction: column;
        }
        .product-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 40px 80px -20px rgba(15, 23, 42, 0.15);
            border-color: var(--brand-yellow);
        }

        .image-container {
            width: 100%;
            height: 240px;
            background: #f1f5f9;
            border-radius: 18px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }
        .product-image { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.6s ease;
        }
        .product-card:hover .product-image { transform: scale(1.1); }

        .price-tag { font-size: 1.75rem; font-weight: 900; color: var(--brand-dark); font-family: 'Outfit'; }
        .flash-tag { 
            position: absolute; 
            top: 1rem; 
            left: 1rem; 
            background: #ef4444; 
            color: #fff; 
            padding: 6px 12px; 
            border-radius: 10px; 
            font-size: 0.7rem; 
            font-weight: 900; 
            text-transform: uppercase;
            z-index: 5;
        }

        .stock-indicator {
            margin-top: auto;
            padding-top: 1.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    </style>

    {{-- Hero Section --}}
    <div class="hero-section">
        <h1 class="hero-title">Experience the Future<br><span>Shopping</span> at GO.Market.</h1>
        <p class="hero-subtitle">The most intelligent e-commerce ecosystem. Shop with speed, security, and smart features designed for a premium experience.</p>
    </div>

    {{-- Search & Filters --}}
    <div class="search-container">
        <select wire:model.live="catalogBranchId" class="filter-select">
            <option value="">Global Inventory</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="selectedCategoryId" class="filter-select">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <input type="text" 
               wire:model.live.debounce.300ms="search" 
               placeholder="Search entire collection..." 
               class="search-input">
    </div>

    {{-- Product Grid --}}
    <div class="product-grid">
        @forelse($products as $product)
            <div class="product-card" wire:key="prod-{{ $product->id }}">
                
                @if($product->on_flash_sale)
                    <div class="flash-tag">⚡ Flash Sale</div>
                @endif

                <div class="wishlist-position" style="position: absolute; top: 1.25rem; right: 1.25rem; z-index: 10;">
                    @livewire('wishlist-button', ['productId' => $product->id], key('wish-'.$product->id))
                </div>

                <a href="{{ route('products.show', $product->id) }}" style="text-decoration: none; color: inherit;">
                    <div class="image-container">
                        <img src="{{ optional($product->primaryImage)->path ? asset('storage/' . $product->primaryImage->path) : ($product->image_url ?? 'https://via.placeholder.com/400x300') }}" 
                             class="product-image" 
                             alt="{{ $product->name }}">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">{{ $product->category->name }}</span>
                        <h3 style="margin: 0.25rem 0 0.5rem; font-size: 1.3rem; line-height: 1.2;">{{ $product->name }}</h3>
                        <p style="color: #64748b; font-size: 0.85rem; line-height: 1.5; margin: 0;">
                            {{ Str::limit($product->description, 80) }}
                        </p>
                    </div>
                </a>

                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1.5rem;">
                    <div>
                        @if($product->on_flash_sale)
                            <span class="price-tag" style="color: #ef4444;">${{ number_format($product->current_price, 2) }}</span>
                            <div style="font-size: 0.8rem; color: #94a3b8; text-decoration: line-through;">
                                ${{ number_format($product->price, 2) }}
                            </div>
                        @else
                            <span class="price-tag">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    @livewire('storefront-add-to-cart', [
                        'productId' => $product->id, 
                        'showIcon' => true, 
                        'buttonClass' => 'btn btn-yellow'
                    ], key('add-to-bag-'.$product->id))
                </div>

                <div class="stock-indicator">
                    @if($product->stock > 10)
                        <span class="dot" style="background: #22c55e;"></span>
                        <span style="color: #22c55e;">AVAILABLE IN STOCK</span>
                    @elseif($product->stock > 0)
                        <span class="dot" style="background: #f59e0b;"></span>
                        <span style="color: #f59e0b;">LOW STOCK ({{ $product->stock }} LEFT)</span>
                    @else
                        <span class="dot" style="background: #ef4444;"></span>
                        <span style="color: #ef4444;">SOLD OUT</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 6rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                <h3 style="color: #94a3b8; font-weight: 700;">No equipment found in this branch.</h3>
                <p style="color: #cbd5e1;">Try adjusting your filters or searching for something else.</p>
            </div>
        @endforelse
    </div>
</div>