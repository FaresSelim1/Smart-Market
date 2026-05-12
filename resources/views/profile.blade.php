@extends('layouts.app')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            {{ substr($user->name, 0, 1) }}
        </div>
        <div class="profile-title">
            <h1>My Profile</h1>
            <p>Manage your account settings and view your information.</p>
        </div>
    </div>

    <div class="profile-grid">
        <div class="profile-card">
            <div class="card-section">
                <h3>Personal Information</h3>
                <div class="info-item">
                    <label>Full Name</label>
                    <span>{{ $user->name }}</span>
                </div>
                <div class="info-item">
                    <label>Email Address</label>
                    <span>{{ $user->email }}</span>
                </div>
            </div>

            <div class="card-section">
                <h3>Account Details</h3>
                <div class="info-item">
                    <label>Account Role</label>
                    <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role ?? 'user') }}</span>
                </div>
                <div class="info-item">
                    <label>Joined Date</label>
                    <span>{{ $user->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="profile-actions">
            <div class="action-card">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="/orders" class="btn btn-yellow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        View Orders
                    </a>
                    <a href="{{ route('wishlist') }}" class="btn btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        My Wishlist
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 3rem;
        animation: fadeInDown 0.6s ease-out;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        background: var(--brand-yellow);
        color: var(--brand-dark);
        font-size: 3rem;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 30px;
        box-shadow: 0 20px 25px -5px var(--brand-yellow-glow);
        font-family: 'Outfit', sans-serif;
    }

    .profile-title h1 {
        margin: 0;
        font-size: 2.5rem;
        color: var(--brand-dark);
    }

    .profile-title p {
        margin: 0.5rem 0 0;
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .profile-card, .action-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: var(--shadow-premium);
        border: 1px solid var(--glass-border);
    }

    .card-section {
        margin-bottom: 2rem;
    }

    .card-section:last-child {
        margin-bottom: 0;
    }

    .card-section h3, .action-card h3 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        color: var(--brand-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-item {
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-item label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 700;
    }

    .info-item span {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--brand-dark);
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 800;
    }

    .role-badge.admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-badge.user {
        background: #f1f5f9;
        color: #475569;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid var(--brand-dark);
        color: var(--brand-dark);
    }

    .btn-outline:hover {
        background: var(--brand-dark);
        color: white;
    }

    .btn-danger {
        background: #fee2e2;
        color: #991b1b;
        width: 100%;
        justify-content: center;
    }

    .btn-danger:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
    }
</style>
@endsection
