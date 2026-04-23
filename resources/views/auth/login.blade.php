<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Kiki Hijab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F3A1BC] min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 w-full max-w-md mx-4">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-[#F3A1BC] rounded-2xl flex items-center justify-center text-4xl font-bold text-white">K</div>
        </div>
        <h1 class="text-3xl font-bold text-center mb-1">KIKI HIJAB</h1>
        <p class="text-center text-gray-500 text-sm mb-8">Masuk untuk melanjutkan belanja</p>

        @if (session('status'))
            <div class="mb-4 bg-green-50 text-green-700 text-sm rounded-2xl px-4 py-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('email')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('password')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center justify-between px-1">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="accent-[#F3A1BC] w-4 h-4"> Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-[#F3A1BC] hover:underline">Lupa password?</a>
                @endif
            </div>
            <button type="submit" class="w-full bg-black text-white py-4 rounded-3xl font-semibold text-base hover:bg-gray-800 transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun? <a href="{{ route('register') }}" class="text-[#F3A1BC] font-semibold hover:underline">Daftar sekarang</a>
        </p>
        <div class="mt-4 text-center">
            <a href="{{ route('pembeli.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Toko</a>
        </div>
    </div>
</body>
</html>
