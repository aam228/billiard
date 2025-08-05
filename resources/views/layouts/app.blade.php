<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Billiard Management System</title>

    <script>
        // Skrip ini berjalan sebelum halaman dirender untuk mencegah "flash" tema
        (function() {
            const theme = localStorage.getItem('theme') || '{{ Auth::check() ? Auth::user()->theme : 'light' }}';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    {{-- File ini kemungkinan besar tidak lagi diperlukan karena tema diatur oleh Tailwind --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/tema.css') }}"> --}}

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @yield('head')
    @stack('styles')
</head>
<body class="bg-zinc-100 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-zinc-200 dark:bg-zinc-800">
        @auth
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black opacity-50 transition-opacity lg:hidden"></div>

            {{-- DIUBAH: Sidebar utama menggunakan dark:bg-zinc-900 --}}
            <nav 
                :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
                class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-white dark:bg-zinc-900 shadow-lg lg:translate-x-0 lg:static lg:inset-0"
            >
                <div class="flex items-center justify-center p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="text-center">
                        <h4 class="text-xl font-bold text-zinc-800 dark:text-white">Admin System</h4>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Sistem Informasi</div>
                    </div>
                </div>
                
                <div class="p-4 space-y-6">
                    @php
                        function nav_link($route, $icon, $label, $activeCondition) {
                            $isActive = request()->routeIs($activeCondition);
                            $linkClasses = 'flex items-center px-4 py-2 rounded-lg transition-colors duration-200 ';
                            // DIUBAH: Warna link di dark mode menggunakan zinc
                            $linkClasses .= $isActive 
                                ? 'bg-gray-200 dark:bg-zinc-700 text-gray-900 dark:text-white shadow-inner'
                                : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700';

                            return '
                                <a href="' . route($route) . '" class="' . $linkClasses . '">
                                    <i class="w-6 text-center ' . $icon . '"></i>
                                    <span class="mx-4 font-medium">' . $label . '</span>
                                </a>';
                        }
                    @endphp

                    <div>
                        <h3 class="mb-2 text-xs font-semibold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Main Menu</h3>
                        {!! nav_link('dashboard', 'fas fa-tachometer-alt', 'Dashboard', 'dashboard') !!}
                    </div>

                    <div>
                        <h3 class="mb-2 text-xs font-semibold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Operations</h3>
                        <div class="space-y-2">
                            {!! nav_link('meja.index', 'fas fa-table', 'Menejemen Meja', 'meja.*') !!}
                            {!! nav_link('transaksi.histori', 'fas fa-history', 'Riwayat Transaksi', 'transaksi.histori') !!}
                            {!! nav_link('produk.index', 'fas fa-utensils', 'Produk', 'produk.*') !!}
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-2 text-xs font-semibold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Analytics</h3>
                        <div class="space-y-2">
                            {!! nav_link('analytics.index', 'fas fa-chart-line', 'Chart', 'analytics.*') !!}
                            {!! nav_link('settings.index', 'fas fa-cogs', 'Settings', 'settings.*') !!}
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-0 w-full p-4 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center mb-4">
                        <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/default_profile.png') }}" 
                             alt="User Profile" 
                             class="w-10 h-10 rounded-full object-cover mr-3">
                        <div>
                            <h6 class="font-semibold text-zinc-800 dark:text-white">{{ Auth::user()->name }}</h6>
                            <small class="text-zinc-500 dark:text-zinc-400">Administrator</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>
        @endauth
        
        <div class="flex-1 flex flex-col overflow-hidden">
            @auth
            {{-- DIUBAH: Header mobile menggunakan dark:bg-zinc-900 --}}
            <header class="flex items-center justify-between p-4 bg-white dark:bg-zinc-900 border-b dark:border-zinc-700 lg:hidden">
                <a class="text-xl font-bold text-zinc-800 dark:text-white" href="#">Billiard Pro</a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-zinc-500 dark:text-zinc-400 focus:outline-none focus:text-zinc-300">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </header>
            @endauth

            {{-- DIUBAH: Latar konten utama menggunakan dark:bg-zinc-800 --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-zinc-100 dark:bg-zinc-800">
                <div class="container mx-auto px-6 py-8">
                    @guest
                    <nav class="bg-white dark:bg-zinc-900 shadow-md rounded-lg p-4 mb-8">
                        <div class="container mx-auto flex justify-between items-center">
                            <a class="text-xl font-bold flex items-center text-zinc-800 dark:text-white" href="#">
                                <i class="fas fa-8ball text-blue-600 mr-2"></i>
                                Billiard Pro
                            </a>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="flex items-center text-zinc-600 dark:text-zinc-300 hover:text-blue-600">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                                </a>
                                @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="flex items-center text-zinc-600 dark:text-zinc-300 hover:text-blue-600">
                                    <i class="fas fa-user-plus mr-1"></i>Register
                                </a>
                                @endif
                            </div>
                        </div>
                    </nav>
                    @endguest

                    @if(isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif

                    @yield('content')
                    
                </div>
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>