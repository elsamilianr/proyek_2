<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kiki Hijab') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { background-color: #F3A1BC; font-family: 'Segoe UI', sans-serif; }

        .category-chip { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .category-chip.active {
            background-color: #F3A1BC !important;
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(243,161,188,0.5);
        }

        .size-btn { transition: all 0.2s ease; }
        .size-btn.active {
            background-color: #F3A1BC;
            color: #fff;
            border-color: #F3A1BC;
        }

        .product-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }

        .accordion-header { transition: all 0.2s ease; }
        .accordion-header:hover { color: #F3A1BC; }
        .toggle-icon { transition: transform 0.3s ease; }
    </style>

    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="bg-[#F3A1BC] border-b border-pink-300 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            {{-- Kiri: Nav links --}}
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-white">
                <a href="{{ route('pembeli.index') }}" class="hover:underline">Beranda</a>
                <a href="{{ route('pembeli.index', ['kategori' => 'hijab']) }}" class="hover:underline">Koleksi</a>
            </div>

            {{-- Tengah: Logo --}}
            <a href="{{ route('pembeli.index') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 bg-white rounded-2xl flex items-center justify-center text-[#F3A1BC] text-2xl font-bold">K</div>
                <h1 class="text-2xl font-bold tracking-tighter text-white">KIKI HIJAB</h1>
            </a>

            {{-- Kanan: Cart + User --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('pembeli.keranjang') }}" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-3xl hover:bg-gray-800 transition text-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="hidden sm:inline font-medium">Keranjang</span>
                        <span class="bg-white text-black text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $cartCount ?? 0 }}</span>
                    </a>

                    {{-- User dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-white hover:scale-105 transition">
                            <div class="w-8 h-8 rounded-2xl bg-white/30 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->nama }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-1 w-44 bg-white rounded-2xl shadow-xl py-2 hidden group-hover:block z-50">
                            <a href="{{ route('pembeli.pesanan.index') }}" class="block px-4 py-2 text-sm hover:bg-pink-50">
                                <i class="fas fa-box mr-2 text-[#F3A1BC]"></i> Pesanan Saya
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-pink-50 text-red-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-black text-white px-4 py-2 rounded-3xl text-sm font-medium hover:bg-gray-800 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-white text-black px-4 py-2 rounded-3xl text-sm font-medium hover:bg-gray-100 transition hidden sm:block">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
    <div id="flash-success" class="fixed top-4 right-4 z-[9999] bg-black text-white px-6 py-3 rounded-3xl shadow-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-green-400"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white/60 hover:text-white">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('flash-success')?.remove(), 3500);</script>
@endif

@if(session('error'))
    <div id="flash-error" class="fixed top-4 right-4 z-[9999] bg-red-600 text-white px-6 py-3 rounded-3xl shadow-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white/60 hover:text-white">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('flash-error')?.remove(), 4000);</script>
@endif

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    {{ $slot }}
</main>

{{-- Footer --}}
<footer class="mt-16 bg-black text-white py-8 text-center text-sm">
    <p class="text-pink-300 font-bold text-lg mb-1">KIKI HIJAB</p>
    <p class="text-gray-400">© {{ date('Y') }} Kiki Hijab. Semua hak dilindungi.</p>
</footer>

@stack('scripts')
</body>
</html>
