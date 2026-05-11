<div>
    @if (session()->has('message'))
        <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; padding: 1rem; border-radius: 12px; text-align: center; font-weight: 700; margin-bottom: 1rem; animation: slideIn 0.3s ease;">
            {{ session('message') }}
        </div>
    @endif
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
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>

