<div style="position: relative;">
    @if (session()->has('message'))
        <div style="position: absolute; bottom: 100%; left: 0; right: 0; margin-bottom: 10px; background: rgba(15,23,42,0.95); color: #fff; padding: 0.75rem; border-radius: 12px; text-align: center; font-weight: 700; font-size: 0.8rem; z-index: 50; pointer-events: none; animation: fadeUp 0.3s ease;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="position: absolute; bottom: 100%; left: 0; right: 0; margin-bottom: 10px; background: rgba(239, 68, 68, 0.95); color: #fff; padding: 0.75rem; border-radius: 12px; text-align: center; font-weight: 700; font-size: 0.8rem; z-index: 50; pointer-events: none; animation: fadeUp 0.3s ease;">
            {{ session('error') }}
        </div>
    @endif

    <button
        wire:click="addToCart"
        wire:loading.attr="disabled"
        wire:target="addToCart"
        @disabled($isOutOfStock)
        class="{{ $buttonClass }} {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed' : '' }}"
        @if($isOutOfStock) style="background: #94a3b8; border-color: #94a3b8; cursor: not-allowed;" @endif
    >
        <span wire:loading.remove wire:target="addToCart" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            @if($isOutOfStock)
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                {{ $showIcon ? 'N/A' : 'Out of Stock' }}
            @else
                @if($showIcon)
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                @endif
                {{ $showIcon ? 'Add' : '+ Add to Bag' }}
            @endif
        </span>
        <span wire:loading wire:target="addToCart">
            {{ $showIcon ? '...' : 'Adding...' }}
        </span>
    </button>
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>

