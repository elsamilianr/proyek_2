<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.checkout') }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Checkout
        </a>
    </div>

    <h1 class="text-3xl font-bold text-white mb-8">Upload Bukti Pembayaran</h1>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Info Pesanan dari Session --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-3">Ringkasan Pesanan</h2>
            <div class="divide-y divide-gray-100">
                @foreach($keranjang->details as $detail)
                    <div class="py-3 flex justify-between items-center text-sm text-gray-700">
                        <span>{{ $detail->varian?->produk?->nama_produk ?? '-' }}
                            <span class="text-gray-400">× {{ $detail->jumlah }}</span>
                        </span>
                        <span class="font-medium">Rp {{ number_format($detail->jumlah * ($detail->varian->harga ?? 0), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center mt-4 pt-3 border-t">
                <span class="text-sm text-gray-500">Metode</span>
                <span class="font-medium text-sm">Transfer Bank</span>
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
            </div>
        </div>

        {{-- Form Upload --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-5">Upload Bukti Transfer</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 rounded-2xl p-4 mb-4 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pembeli.pembayaran.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Upload file --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Transfer</label>
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
                    <i class="fas fa-paper-plane mr-2"></i> Kirim & Buat Pesanan
                </button>
                <p class="text-xs text-gray-400 text-center mt-3">
                    Pesanan akan dibuat setelah bukti dikirim. Tim kami memverifikasi dalam 1×24 jam kerja.
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