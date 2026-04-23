<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.pesanan.show', $pesanan->id) }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail Pesanan
        </a>
    </div>

    <h1 class="text-3xl font-bold text-white mb-8">Upload Bukti Pembayaran</h1>

    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Info Pesanan --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-3">Ringkasan Pesanan</h2>
            <div class="flex justify-between items-center text-sm text-gray-600">
                <span>Kode Pesanan</span>
                <span class="font-bold text-base text-gray-900">{{ $pesanan->kode_pesanan }}</span>
            </div>
            <div class="flex justify-between items-center text-sm text-gray-600 mt-2">
                <span>Total yang harus dibayar</span>
                <span class="font-bold text-xl text-[#F3A1BC]">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Info Rekening --}}
        <div class="bg-pink-50 border border-pink-200 rounded-3xl p-6">
            <h2 class="text-base font-bold text-pink-700 mb-4">
                <i class="fas fa-university mr-2"></i> Info Transfer
            </h2>
            <div class="space-y-3 text-sm">
                <div class="bg-white rounded-2xl p-4 flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs">Bank BCA</p>
                        <p class="font-bold text-lg tracking-widest">1234 5678 90</p>
                        <p class="text-gray-600">a.n. Kiki Hijab</p>
                    </div>
                    <button onclick="navigator.clipboard.writeText('1234567890'); alert('Nomor rekening disalin!')"
                        class="text-pink-500 hover:text-pink-700 text-xs">
                        <i class="fas fa-copy mr-1"></i>Salin
                    </button>
                </div>
                <div class="bg-white rounded-2xl p-4 flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 text-xs">QRIS</p>
                        <p class="text-gray-600 text-sm">Scan QRIS di toko atau hubungi admin</p>
                    </div>
                    <i class="fas fa-qrcode text-pink-400 text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Form Upload --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-5">Upload Bukti Pembayaran</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 rounded-2xl p-4 mb-4 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pembeli.pembayaran.upload', $pesanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Metode --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="flex gap-3 flex-wrap">
                        @foreach(['transfer_bank' => 'Transfer Bank', 'qris' => 'QRIS', 'cod' => 'COD'] as $val => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="metode" value="{{ $val }}" class="hidden peer" {{ old('metode', 'transfer_bank') === $val ? 'checked' : '' }}>
                                <span class="block px-4 py-3 border border-gray-300 rounded-2xl text-sm font-medium transition
                                    peer-checked:border-[#F3A1BC] peer-checked:bg-pink-50 peer-checked:text-pink-600 hover:border-pink-300">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Upload file --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Transfer / Struk</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-pink-300 transition cursor-pointer"
                        onclick="document.getElementById('bukti-input').click()">
                        <div id="preview-wrapper">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Klik untuk pilih gambar</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 5MB</p>
                        </div>
                        <img id="preview-img" class="hidden mx-auto max-h-48 rounded-2xl object-contain" alt="Preview">
                    </div>
                    <input type="file" id="bukti-input" name="bukti_pembayaran" accept="image/*" class="hidden"
                        onchange="previewBukti(event)" required>
                    @error('bukti_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-black text-white py-4 rounded-3xl font-semibold text-base hover:bg-gray-800 transition">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Bukti Pembayaran
                </button>
                <p class="text-xs text-gray-400 text-center mt-3">
                    Tim kami akan memverifikasi pembayaranmu dalam 1×24 jam kerja.
                </p>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewBukti(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                document.getElementById('preview-wrapper').classList.add('hidden');
                const img = document.getElementById('preview-img');
                img.src = ev.target.result;
                img.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    </script>
    @endpush
</x-pembeli-layout>
