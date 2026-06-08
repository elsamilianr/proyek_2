<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Kiki Hijab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F3A1BC] min-h-screen flex items-center justify-center font-sans py-10">
    <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 w-full max-w-md mx-4">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
        </div>
        <h1 class="text-3xl font-bold text-center mb-1">KIKI HIJAB</h1>
        <p class="text-center text-gray-500 text-sm mb-8">Buat akun untuk mulai belanja</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama Lengkap" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('nama')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Username" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('username')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('email')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
                @error('password')<p class="text-red-500 text-xs mt-1 pl-2">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required
                    class="w-full px-6 py-4 rounded-3xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#F3A1BC] text-sm">
            </div>
            <button type="submit" class="w-full bg-black text-white py-4 rounded-3xl font-semibold text-base hover:bg-gray-800 transition">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-[#F3A1BC] font-semibold hover:underline">Masuk</a>
        </p>
        <div class="mt-4 text-center">
            <a href="{{ route('pembeli.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Toko</a>
        </div>
    </div>
</body>
</html>