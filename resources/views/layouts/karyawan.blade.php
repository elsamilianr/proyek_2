<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hayki - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/karyawan.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    {{-- NAVBAR --}}
    <nav class="navbar">
        <a href="{{ route('karyawan.dashboard') }}" class="navbar-brand">
            <div class="navbar-logo-placeholder">H</div>
            <span class="navbar-title">Hayki</span>
        </a>
        <div class="navbar-user">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A9 9 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ auth()->user()->name ?? 'Karyawan 1' }}</span>
        </div>
    </nav>

    <div class="main-content">
        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li class="{{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.dashboard') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.produk*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.produk.index') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Produk
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.varian*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.varian.index') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                        </svg>
                        Stok
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.pesanan*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.pesanan.index') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pesanan
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.riwayat*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.riwayat.index') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.transaksi*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.transaksi.create') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Transaksi Offline
                    </a>
                </li>
                <li class="{{ request()->routeIs('karyawan.promo*') ? 'active' : '' }}">
                    <a href="{{ route('karyawan.promo.index') }}">
                        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Promo
                    </a>
                </li>
            </ul>
        </aside>

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>