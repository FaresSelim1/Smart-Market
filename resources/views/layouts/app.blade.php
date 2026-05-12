<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART MARKET — Premium Protection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { 
            --brand-yellow: #facc15; 
            --brand-yellow-glow: rgba(250, 204, 21, 0.4);
            --brand-dark: #0f172a; 
            --brand-card: rgba(255, 255, 255, 0.8);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow-premium: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            --radius-lg: 24px;
            --radius-md: 16px;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(250, 204, 21, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(15, 23, 42, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main); 
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, .logo {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        nav { 
            background: var(--bg-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            height: 90px; 
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo { 
            font-size: 1.8rem; 
            font-weight: 900; 
            text-decoration: none; 
            color: var(--brand-dark); 
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .logo span { color: var(--brand-yellow); }

        .nav-links { display: flex; align-items: center; gap: 2.5rem; }

        .nav-links a { 
            text-decoration: none; 
            color: var(--text-muted); 
            font-weight: 600; 
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            position: relative;
        }

        .nav-links a:hover { color: var(--brand-dark); }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--brand-yellow);
            transition: width 0.3s ease;
        }
        .nav-links a:hover::after { width: 100%; }

        .cart-link {
            background: var(--brand-dark) !important;
            color: #fff !important;
            padding: 12px 24px !important;
            border-radius: 14px !important;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
            position: relative;
        }

        .cart-badge {
            background: var(--brand-yellow);
            color: var(--brand-dark);
            font-size: 0.7rem;
            font-weight: 900;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid var(--brand-dark);
        }

        .cart-link:hover {
            transform: translateY(-2px);
            background: #1e293b !important;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.25);
        }

        .container { 
            max-width: 1280px; 
            margin: 4rem auto; 
            padding: 0 2rem; 
        }

        .card {
            background: var(--brand-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: var(--shadow-premium);
        }

        .btn { 
            padding: 1rem 2rem; 
            border-radius: 14px; 
            border: none; 
            cursor: pointer; 
            font-weight: 800; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-yellow { 
            background: var(--brand-yellow); 
            color: var(--brand-dark); 
            box-shadow: 0 4px 14px 0 var(--brand-yellow-glow);
        }

        .btn-yellow:hover { 
            transform: scale(1.05);
            box-shadow: 0 8px 25px 0 var(--brand-yellow-glow);
        }

        .alert {
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .alert-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        .logout-btn { 
            background: none;
            border: none;
            color: #ef4444 !important;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
        }
        .logout-btn:hover { text-decoration: underline; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @livewireStyles
</head>
<body>
    <nav>
        <a href="/" class="logo">SMART<span>.</span>MARKET</a>
        <div class="nav-links">
            <a href="/">Catalog</a>
            @auth
                <a href="/orders">My Orders</a>
                <a href="{{ route('wishlist') }}">Wishlist</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" style="color: var(--brand-dark); font-weight: 800;">Login</a>
                <a href="{{ route('register') }}" style="background: var(--brand-yellow); color: var(--brand-dark); padding: 8px 16px; border-radius: 10px; font-weight: 800;">Register</a>
            @endguest

            <a href="/cart" class="cart-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Bag</span>
                @livewire('cart-count')
            </a>
        </div>
    </nav>

    <div class="container">
        @if(session()->has('message'))
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
        
        <main>
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>

    @livewireScripts
</body>
</html>