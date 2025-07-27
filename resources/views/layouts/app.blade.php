<!DOCTYPE html>
<html lang="en" data-theme="{{ Auth::check() ? Auth::user()->theme : 'light' }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billiard Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tema.css') }}">

    @yield('head')
    @stack('styles')
</head>
<body>
    <!-- Guest Navigation (hanya tampil jika tidak login) -->
    @guest
    <nav class="guest-nav">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-8ball" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                Billiard Pro
            </a>
            <div class="nav-links">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt me-1"></i>Login
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}">
                    <i class="fas fa-user-plus me-1"></i>Register
                </a>
                @endif
            </div>
        </div>
    </nav>
    @endguest

    <div class="dashboard-container">
        @auth
        <!-- Mobile menu toggle button -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Overlay for mobile -->
        <div class="overlay" id="overlay"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <h4>Admin System</h4>
                <div class="subtitle">Sistem Informasi</div>
            </div>
            
            <!-- Navigation Menu -->
            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main Menu</div>
                    <div class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Operations</div>
                    <div class="nav-item">
                        <a href="{{ route('meja.index') }}" class="nav-link {{ request()->routeIs('meja.*') ? 'active' : '' }}">
                            <i class="fas fa-table"></i>
                            <span>Manage Tables</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('transaksi.histori') }}" class="nav-link {{ request()->routeIs('transaksi.histori') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Transaction History</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                            <i class="fas fa-utensils"></i>
                            <span>Food & Beverage</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Analytics</div>
                    <div class="nav-item">
                        <a href="{{ route('analytics.index') }}" class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Chart</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"> {{-- <-- TAMBAHKAN INI --}}
                            <i class="fas fa-cogs"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="user-profile" style="border-bottom: 0px;">
                <div class="user-info">
                    <div class="user-avatar">
                        {{-- Conditional display for profile image or default icon --}}
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="User Profile Image" class="rounded-circle user-profile-image">
                        @else
                            {{-- Fallback if no profile image is set --}}
                            <img src="{{ asset('images/default_profile.png') }}" alt="Default Profile Image" class="rounded-circle user-profile-image">
                        @endif
                    </div>
                    <div class="user-details">
                        <h6>{{ Auth::user()->name }}</h6>
                        <small>Administrator</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">
                        <i class="fas fa-sign-out-alt me-2"></i>Keluar
                    </button>
                </form>
            </div>
        </nav>
        @endauth

        <!-- Main Content -->
        <div class="main-content">
            <div class="@guest guest-content @else content-body @endguest">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Slot header --}}
                {{ $header ?? '' }}

                {{-- Slot konten utama --}}
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        mobileMenuToggle?.addEventListener('click', function() {
            sidebar?.classList.toggle('show');
            overlay?.classList.toggle('show');
        });

        overlay?.addEventListener('click', function() {
            sidebar?.classList.remove('show');
            overlay?.classList.remove('show');
        });

        // Auto close mobile menu when clicking nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar?.classList.remove('show');
                    overlay?.classList.remove('show');
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar?.classList.remove('show');
                overlay?.classList.remove('show');
            }
        });

        // Format currency input
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('tarif_per_jam');
            if (input) {
                input.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
                });

                input.form?.addEventListener('submit', function () {
                    input.value = input.value.replace(/\./g, '');
                });
            }
        });

        // Smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>