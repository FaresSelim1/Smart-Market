@extends('layouts.app')

@section('content')
<div class="product-detail-page" style="min-height: 100vh; padding: 4rem 0;">
    <style>
        .detail-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 4rem;
        }

        /* Glassmorphic Gallery */
        .gallery-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            padding: 2rem;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.05);
            position: sticky;
            top: 2rem;
        }

        .main-image-wrapper {
            width: 100%;
            aspect-ratio: 4/3;
            background: #f8fafc;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
        }

        .main-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .main-image-wrapper:hover .main-image {
            transform: scale(1.05);
        }

        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 1rem;
        }

        .thumb-item {
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            background: #fff;
        }

        .thumb-item.active {
            border-color: var(--brand-yellow);
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.2);
        }

        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .thumb-item:hover img, .thumb-item.active img {
            opacity: 1;
        }

        /* Purchase Panel */
        .info-card {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        .product-meta {
            margin-bottom: 1rem;
        }

        .category-badge {
            background: rgba(251, 191, 36, 0.1);
            color: #b45309;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .product-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            color: var(--brand-dark);
            font-family: 'Outfit';
            margin-bottom: 1.5rem;
        }

        .product-description {
            font-size: 1.15rem;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .price-section {
            background: var(--brand-dark);
            color: #fff;
            padding: 2.5rem;
            border-radius: 28px;
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.3);
            position: relative;
            overflow: hidden;
        }

        .price-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--brand-yellow) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(40px);
        }

        .current-price {
            font-size: 3rem;
            font-weight: 900;
            font-family: 'Outfit';
            display: block;
        }

        .old-price {
            font-size: 1.25rem;
            color: #94a3b8;
            text-decoration: line-through;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-top: 1rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (max-width: 1024px) {
            .detail-container {
                grid-template-columns: 1fr;
            }
            .gallery-card {
                position: relative;
                top: 0;
            }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="detail-container">
        {{-- Left: Visual Experience --}}
        <div class="gallery-card">
            @php($images = $product->images->sortBy('sort_order'))
            @php($primary = $images->first())

            <div class="main-image-wrapper" id="mainImageContainer">
                @if($primary)
                    <img src="{{ asset('storage/' . $primary->path) }}" 
                         id="displayImage"
                         class="main-image" 
                         alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/800x600?text=No+Image+Available" 
                         class="main-image" 
                         alt="No image">
                @endif
            </div>

            @if($images->count() > 1)
                <div class="thumbnail-grid">
                    @foreach($images as $index => $img)
                        <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" 
                             onclick="updateImage('{{ asset('storage/' . $img->path) }}', this)">
                            <img src="{{ asset('storage/' . $img->path) }}" alt="Preview">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right: Content & Conversion --}}
        <div class="info-card">
            <div class="product-meta">
                <span class="category-badge">{{ $product->category->name }}</span>
                <h1 class="product-title">{{ $product->name }}</h1>
                <p class="product-description">{{ $product->description }}</p>
                
                <div style="display: flex; gap: 2rem; border-top: 1px solid #e2e8f0; padding-top: 2rem;">
                    <div>
                        <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">SKU Identifier</div>
                        <div style="font-weight: 700; color: var(--brand-dark);">{{ $product->sku }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Condition</div>
                        <div style="font-weight: 700; color: var(--brand-dark);">New / Factory Sealed</div>
                    </div>
                </div>
            </div>

            <div class="price-section">
                @if($product->on_flash_sale)
                    <span class="old-price">${{ number_format($product->price, 2) }}</span>
                    <span class="current-price" style="color: var(--brand-yellow);">${{ number_format($product->current_price, 2) }}</span>
                    <div style="margin-top: 0.5rem; color: #fbbf24; font-size: 0.8rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">
                        ⚡ 15% Limited Discount Applied
                    </div>
                @else
                    <span class="current-price">${{ number_format($product->price, 2) }}</span>
                @endif

                <div class="stock-status">
                    @php($totalStock = $product->branches->sum('pivot.stock_level'))
                    @if($totalStock > 10)
                        <div class="status-dot" style="background: #22c55e; box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);"></div>
                        <span style="color: #4ade80;">Global Stock: Verified Available</span>
                    @elseif($totalStock > 0)
                        <div class="status-dot" style="background: #fbbf24; box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);"></div>
                        <span style="color: #fbbf24;">Limited Reserve ({{ $totalStock }} Units)</span>
                    @else
                        <div class="status-dot" style="background: #ef4444; box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);"></div>
                        <span style="color: #f87171;">Currently Depleted</span>
                    @endif
                </div>

                <div class="action-buttons">
                    @livewire('storefront-add-to-cart', ['productId' => $product->id])
                    <a href="{{ route('cart') }}" class="btn" style="background: rgba(255,255,255,0.1); color: #fff; padding: 1.25rem; border-radius: 16px; text-align: center; border: 1px solid rgba(255,255,255,0.2); font-weight: 700; text-transform: uppercase;">
                        View Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateImage(url, thumb) {
        // Update main image
        const mainImg = document.getElementById('displayImage');
        mainImg.style.opacity = '0';
        
        setTimeout(() => {
            mainImg.src = url;
            mainImg.style.opacity = '1';
        }, 150);

        // Update active thumbnail
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }
</script>
@endsection
