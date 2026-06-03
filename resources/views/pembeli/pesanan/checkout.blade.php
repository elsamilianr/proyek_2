<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.keranjang') }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Keranjang
        </a>
    </div>

    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-8">Checkout</h1>

    <form id="formCheckout" action="{{ route('pembeli.pesanan.buat') }}" method="POST">
        @csrf

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Detail Pesanan --}}
            <div class="flex-1 space-y-6">

                {{-- Item list --}}
                <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold mb-4">Item Pesanan</h2>

                    <div class="space-y-4">
                        @php $subtotal = 0; @endphp

                        @foreach($keranjang->details as $detail)
                            @php
                                $sub = $detail->jumlah * ($detail->varian->harga ?? 0);
                                $subtotal += $sub;

                                $foto = $detail->varian->produk->foto
                                    ? Storage::url($detail->varian->produk->foto)
                                    : 'https://picsum.photos/id/' . (($detail->varian->produk->id % 50) + 100) . '/80/80';
                            @endphp

                            <div class="flex gap-4 items-center">
                                <img src="{{ $foto }}" class="w-16 h-16 rounded-2xl object-cover">

                                <div class="flex-1">
                                    <p class="font-medium text-sm">
                                        {{ $detail->varian->produk->nama_produk }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $detail->varian->label }} × {{ $detail->jumlah }}
                                    </p>
                                </div>

                                <p class="font-semibold text-sm">
                                    Rp {{ number_format($sub, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Promo --}}
                @if($promos->isNotEmpty())
                    <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
                        <h2 class="text-lg font-bold mb-4">Kode Promo</h2>

                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="promo_id" value="" checked class="accent-[#F3A1BC]">
                                <span class="text-sm text-gray-600">Tanpa promo</span>
                            </label>

                            @foreach($promos as $promo)
                                <label class="flex items-center gap-3 border border-pink-200 rounded-2xl p-3 cursor-pointer hover:bg-pink-50 transition">
                                    <input type="radio" name="promo_id" value="{{ $promo->id }}" class="accent-[#F3A1BC]">

                                    <div>
                                        <p class="font-semibold text-sm text-pink-600">
                                            {{ $promo->nama_promo }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $promo->deskripsi ?? 'Diskon spesial' }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Metode Pembayaran --}}
                <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold mb-4">Metode Pembayaran</h2>

                    <div class="space-y-3">

                        <label class="flex items-center gap-3 border rounded-2xl p-4 cursor-pointer hover:bg-pink-50">
                            <input type="radio" name="metode" value="transfer" required class="accent-[#F3A1BC]">
                            <div>
                                <p class="font-medium text-sm">Transfer Bank</p>
                                <p class="text-xs text-gray-500">Upload bukti transfer setelah checkout</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 border rounded-2xl p-4 cursor-pointer hover:bg-pink-50">
                            <input type="radio" name="metode" value="qris" class="accent-[#F3A1BC]">
                            <div>
                                <p class="font-medium text-sm">QRIS</p>
                                <p class="text-xs text-gray-500">Upload bukti pembayaran QRIS</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 border rounded-2xl p-4 cursor-pointer hover:bg-pink-50">
                            <input type="radio" name="metode" value="cash" class="accent-[#F3A1BC]">
                            <div>
                                <p class="font-medium text-sm">Bayar di Toko</p>
                                <p class="text-xs text-gray-500">Pembayaran dilakukan saat mengambil pesanan</p>
                            </div>
                        </label>

                        {{-- MIDTRANS --}}
                        <label class="flex items-center gap-3 border border-pink-300 rounded-2xl p-4 cursor-pointer hover:bg-pink-50">
                            <input type="radio" name="metode" value="midtrans" class="accent-[#F3A1BC]">
                            <div>
                                <p class="font-medium text-sm">💳 Bayar Online (Midtrans)</p>
                                <p class="text-xs text-gray-500">GoPay, OVO, Transfer Bank, Kartu Kredit & lainnya</p>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- Catatan --}}
                <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold mb-4">Catatan Pesanan</h2>

                    <textarea
                        name="catatan"
                        id="catatan"
                        rows="3"
                        placeholder="Tulis catatan untuk penjual (opsional)..."
                        class="w-full border border-gray-300 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300 resize-none"
                    >{{ old('catatan') }}</textarea>
                </div>

            </div>

            {{-- Ringkasan --}}
            <div class="lg:w-80 flex-shrink-0">
                <div class="bg-white/90 rounded-3xl p-6 shadow-lg sticky top-24">

                    <h2 class="text-xl font-bold mb-6">Total Pembayaran</h2>

                    <div class="space-y-3 text-sm mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between text-gray-400 text-xs">
                            <span>Diskon promo</span>
                            <span>Dihitung otomatis</span>
                        </div>

                        <hr>

                        <div class="flex justify-between text-lg font-bold">
                            <span>Estimasi Total</span>
                            <span class="text-[#F3A1BC]">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Info pembeli --}}
                    <div class="bg-pink-50 rounded-2xl p-3 mb-6 text-sm text-gray-600">
                        <p class="font-medium text-pink-600 mb-1">
                            <i class="fas fa-user mr-1"></i>
                            {{ Auth::user()->nama }}
                        </p>

                        <p class="text-xs">{{ Auth::user()->email }}</p>

                        @if(Auth::user()->no_hp)
                            <p class="text-xs">{{ Auth::user()->no_hp }}</p>
                        @endif
                    </div>

                    <button type="button" id="btnBuatPesanan"
                        class="w-full bg-black text-white py-4 rounded-3xl font-semibold text-base hover:bg-gray-800 transition">
                        <i class="fas fa-check-circle mr-2"></i>
                        Buat Pesanan
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Transfer / QRIS akan lanjut ke upload bukti pembayaran
                    </p>

                </div>
            </div>

        </div>
    </form>

    {{-- Midtrans Snap Script --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
    document.getElementById('btnBuatPesanan').addEventListener('click', async function () {
        const metode = document.querySelector('input[name="metode"]:checked')?.value;

        if (!metode) {
            alert('Pilih metode pembayaran terlebih dahulu.');
            return;
        }

        // Jika bukan Midtrans, submit form biasa
        if (metode !== 'midtrans') {
            document.getElementById('formCheckout').submit();
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

        try {
            // Langkah 1: Buat pesanan via AJAX dengan header XMLHttpRequest
            // agar Laravel mendeteksi sebagai AJAX dan return JSON
            const formData = new FormData(document.getElementById('formCheckout'));

            const resPesanan = await fetch('{{ route("pembeli.pesanan.buat") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!resPesanan.ok) {
                const err = await resPesanan.json();
                throw new Error(err.message ?? 'Gagal membuat pesanan.');
            }

            const pesananData = await resPesanan.json();
            const pesananId   = pesananData.pesanan_id;
            console.log('Response pesanan:', pesananData);
            console.log('Pesanan ID:', pesananId);  

            // Langkah 2: Ambil Snap Token
            const resToken = await fetch(`/midtrans/token/${pesananId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const tokenData = await resToken.json();

            if (tokenData.error) {
                throw new Error(tokenData.error);
            }

            // Langkah 3: Buka popup Snap Midtrans
            snap.pay(tokenData.snap_token, {
                onSuccess: function () {
                    window.location.href = `/pesanan/${pesananId}`;
                },
                onPending: function () {
                    window.location.href = `/pesanan/${pesananId}`;
                },
                onError: function () {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    window.location.href = `/pesanan/${pesananId}`;
                },
                onClose: function () {
                    window.location.href = `/pesanan/${pesananId}`;
                },
            });

        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Buat Pesanan';
        }
    });
    </script>

</x-pembeli-layout>