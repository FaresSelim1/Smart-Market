<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | SMART MARKET</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { 
            --brand-yellow: #facc15; 
            --brand-dark: #0f172a; 
            --radius-lg: 24px;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(250, 204, 21, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.05) 0px, transparent 50%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh;
        }

        .login-card { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(16px);
            padding: 3.5rem; 
            border-radius: var(--radius-lg); 
            box-shadow: 0 40px 80px -20px rgba(15, 23, 42, 0.1); 
            width: 100%; 
            max-width: 440px; 
            border: 1px solid rgba(255, 255, 255, 0.4);
            animation: cardIn 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes cardIn { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .logo { 
            font-family: 'Outfit'; 
            font-weight: 900; 
            font-size: 1.5rem; 
            text-align: center; 
            margin-bottom: 2.5rem; 
            letter-spacing: -0.02em;
        }
        .logo span { color: var(--brand-yellow); }

        h2 { 
            text-align: center; 
            color: var(--brand-dark); 
            margin: 0 0 0.5rem; 
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 2rem;
        }
        p { text-align: center; color: #64748b; margin-bottom: 2.5rem; font-size: 1rem; }

        label { display: block; margin-bottom: 0.6rem; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        
        input { 
            width: 100%; 
            padding: 1rem 1.25rem; 
            margin-bottom: 1.5rem; 
            border: 1px solid #e2e8f0; 
            border-radius: 14px; 
            box-sizing: border-box; 
            transition: all 0.3s; 
            font-family: inherit;
            font-size: 1rem;
            background: #fff;
        }
        input:focus { 
            border-color: var(--brand-yellow); 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.15); 
            transform: translateY(-2px);
        }

        .btn-yellow { 
            background: var(--brand-yellow); 
            border: none; 
            padding: 1.1rem; 
            width: 100%; 
            border-radius: 14px; 
            font-weight: 900; 
            cursor: pointer; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            font-size: 1.1rem;
            font-family: 'Outfit';
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--brand-dark);
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(250, 204, 21, 0.3);
        }
        .btn-yellow:hover { 
            transform: scale(1.02); 
            box-shadow: 0 20px 25px -5px rgba(250, 204, 21, 0.4);
        }

        .error { color: #ef4444; font-size: 0.85rem; margin-top: -1.2rem; margin-bottom: 1.2rem; display: block; font-weight: 600; }
        
        .footer-links { text-align: center; margin-top: 2rem; font-size: 0.95rem; color: #64748b; }
        .link { color: var(--brand-dark); text-decoration: none; font-weight: 800; border-bottom: 2px solid var(--brand-yellow); padding-bottom: 2px; }
        .link:hover { background: var(--brand-yellow); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">SMART<span>.</span>MARKET</div>
        <h2>Access Portal</h2>
        <p>Sign in to manage your premium protection systems.</p>
        
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="email">Institutional Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@domain.com" required autofocus>
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password">Security Key</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                @error('password') <span class="error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-yellow">Initialize Session</button>
        </form>

        <div class="footer-links">
            New operator? <a href="{{ route('register') }}" class="link">Create account</a>
        </div>
    </div>
</body>
</html>