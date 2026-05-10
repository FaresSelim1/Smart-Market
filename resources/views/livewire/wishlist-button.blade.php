<div class="wishlist-btn-wrapper">
    <button wire:click="toggleWishlist" 
            wire:loading.attr="disabled"
            class="wishlist-heart-btn"
            style="border:none; background:none; cursor:pointer; outline:none;">
        <svg xmlns="http://www.w3.org/2000/svg" 
             style="width:24px; height:24px; transition: all 0.3s ease; fill: {{ $isWishlisted ? '#ef4444' : 'none' }}; stroke: {{ $isWishlisted ? '#ef4444' : '#64748b' }};" 
             viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </button>
</div>