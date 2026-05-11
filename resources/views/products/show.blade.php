@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="product-grid" style="display:grid; grid-template-columns: 1fr 350px; gap:2rem;">
            {{-- LEFT: Gallery + under-image details --}}
            <div style="background:#fff; border-radius:20px; padding:2rem; border:1px solid #e2e8f0;">
                <h2 style="margin:0 0 1.5rem 0; font-weight:800;">Product Details</h2>

                @php($images = $product->images)
                @php($primary = $images->sortBy('sort_order')->first())

                @if($images->count())
                    <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px; margin-bottom:16px;">
                        @foreach($images as $img)
                            <div style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#f8fafc;">
                                <img
                                    src="{{ asset('storage/' . $img->path) }}"
                                    alt="{{ $product->name }}"
                                    style="width:100%; height:110px; object-fit:cover; display:block;"
                                >
                            </div>
                        @endforeach
                    </div>

                    @if($primary)
                        <div style="border:1px solid #e2e8f0; border-radius:18px; overflow:hidden; background:#f8fafc;">
                            <img
                                src="{{ asset('storage/' . $primary->path) }}"
                                alt="{{ $product->name }}"
                                style="width:100%; height:240px; object-fit:cover; display:block;"
                            >
                        </div>
                    @endif
                @else
                    <div style="border:1px solid #e2e8f0; border-radius:18px; overflow:hidden; background:#f8fafc;">
                        <img
                            src="https://via.placeholder.com/800x600"
                            alt="No image"
                            style="width:100%; height:240px; object-fit:cover; display:block;"
                        >
                    </div>
                @endif

                {{-- Details BELOW the main image (not overlayed) --}}
                <div style="margin-top:1.25rem;">
                    <div style="font-size:0.8rem; color:#64748b; font-weight:900; text-transform:uppercase;">
                        {{ $product->category?->name ?? 'PRODUCT' }}
                    </div>

                    <h3 style="margin:0.5rem 0 0.75rem 0; font-weight:900; font-size:1.6rem; line-height:1.2; color: var(--brand-dark);">
                        {{ $product->name }}
                    </h3>

                    <div style="font-weight:900; font-size:1.4rem; color: var(--brand-yellow); margin-bottom:0.85rem;">
                        ${{ number_format((float) $product->price, 2) }}
                    </div>

                    <p style="margin:0; color:#334155; line-height:1.7; font-size:1rem;">
                        {{ $product->description }}
                    </p>
                </div>
            </div>


            {{-- RIGHT: Purchase panel (unchanged checkout logic) --}}
            <div style="background: var(--brand-dark); color:#fff; border-radius:24px; padding:2rem; height:fit-content;">
                {{-- Buy/add-to-cart only (no name/description here) --}}
                <div style="margin-top:0;">
                    @auth
                        <livewire:storefront-add-to-cart :productId="$product->id" />
                        <a
                            href="{{ route('cart') }}"
                            class="btn btn-yellow"
                            style="display:block; width:100%; margin-top:1.25rem; padding:1rem; font-size:1rem; text-transform:uppercase; text-align:center;"
                        >
                            Buy now
                        </a>
                    @else
                        <div style="margin-top:1rem; color:rgba(255,255,255,0.85); font-weight:800;">
                            Please login to add to cart.
                        </div>
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-yellow"
                            style="display:block; width:100%; margin-top:1.25rem; padding:1rem; font-size:1rem; text-transform:uppercase; text-align:center;"
                        >
                            Login
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </div>
@endsection

