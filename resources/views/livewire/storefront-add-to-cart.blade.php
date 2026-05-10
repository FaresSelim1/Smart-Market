<div>
    @if(auth()->check())
        <button
            wire:click="addToCart"
            wire:loading.attr="disabled"
            class="btn btn-yellow px-6 py-3 font-bold w-full"
        >
            <span wire:loading.remove>+ Add to cart</span>
            <span wire:loading>Adding...</span>
        </button>
    @else
        <a href="{{ route('login') }}" class="btn btn-yellow px-6 py-3 font-bold w-full text-center">
            Login to add
        </a>
    @endif
</div>

