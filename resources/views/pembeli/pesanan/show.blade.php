<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.pesanan.index') }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Pesanan Saya
        </a>
    </div>

    @php
        $statusColor = match($pesanan->status_pesanan) {
            'menunggu_pembayaran'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'menunggu_verifikasi' => 'bg-blue-100 text-blue-700 border-blue-200',
            'diproses'            => 'bg-purple-100 text-purple-700 border-purple-200',
            'selesai'             => 'bg-green-100 text-green-700 border-green-200',
            'dibatalkan'          => 'bg-red-100 text-red-700 border-red-200',
            default               => 'bg-gray-100 text-gray-600 border-gray-200',
        };
        $statusLabel = match($pesanan->status_pesanan) {
            'menunggu_pembayaran'  => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diproses'            => 'Sedang Diproses',
            'selesai'             => 'Selesai',
            'dibatalkan'          => 'Dibatalkan',
            default               => ucfirst($pesanan->status_pesanan),
        };
    @endphp

    <div class="space-y-6">
        {{-- Header Pesanan --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">{{ $pesanan->kode_pesanan }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Dibuat {{ $pesanan->tanggal ? $pesanan->tanggal->format('d M Y') : '-' }}
                    </p>
                </div>
                <span class="self-start sm:self-center text-sm font-semibold px-4 py-2 rounded-2xl border {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Progress bar --}}
            @php
                $steps = ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'selesai'];
                $currentIdx = array_search($pesanan->status_pesanan, $steps);
            @endphp
            @if($pesanan->status_pesanan !== 'dibatalkan')
                <div class="mt-6">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 z-0"></div>
                        <div class="absolute top-4 left-0 h-0.5 bg-[#F3A1BC] z-0 transition-all"
                            style="width: {{ $currentIdx !== false ? ($currentIdx / (count($steps)-1)) * 100 : 0 }}%"></div>
                        @foreach(['Bayar', 'Verifikasi', 'Diproses', 'Selesai'] as $i => $step)
                            <div class="relative z-10 flex flex-col items-center gap-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ ($currentIdx !== false && $i <= $currentIdx) ? 'bg-[#F3A1BC] text-white' : 'bg-gray-200 text-gray-500' }}">
                                    {{ $i + 1 }}
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $step }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Item Pesanan --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-4">Item Dipesan</h2>
            <div class="divide-y divide-gray-100">
                @foreach($pesanan->details as $detail)
                    @php
                        $foto = $detail->varian?->produk?->foto
                            ? Storage::url($detail->varian->produk->foto)
                            : 'https://picsum.photos/id/' . (($detail->varian?->produk?->id % 50 ?? 1) + 100) . '/80/80';
                    @endphp
                    <div class="py-4 flex gap-4 items-center">
                        <img src="{{ $foto }}" class="w-16 h-16 rounded-2xl object-cover">
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $detail->varian?->produk?->nama_produk ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->varian?->label ?? '-' }} × {{ $detail->jumlah }}</p>
                        </div>
                        <p class="font-semibold text-sm">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Harga --}}
        <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-4">Rincian Harga</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($pesanan->diskon > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon {{ $pesanan->promo?->kode }}</span>
                        <span>- Rp {{ number_format($pesanan->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif
                <hr>
                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span class="text-[#F3A1BC]">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Status Pembayaran --}}
        @if($pesanan->pembayaran)
            <div class="bg-white/90 rounded-3xl p-6 shadow-sm">
                <h2 class="text-lg font-bold mb-4">Status Pembayaran</h2>
                <div class="flex gap-4 items-start">
                    @if($pesanan->pembayaran->bukti_pembayaran)
                        <img src="{{ Storage::url($pesanan->pembayaran->bukti_pembayaran) }}"
                            class="w-24 h-24 rounded-2xl object-cover border" alt="Bukti pembayaran">
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-700">Metode: {{ ucfirst(str_replace('_', ' ', $pesanan->pembayaran->metode)) }}</p>
                        <p class="text-sm text-gray-500">Status: <span class="font-semibold">{{ $pesanan->pembayaran->label_status }}</span></p>
                        @if($pesanan->pembayaran->catatan)
                            <p class="text-xs text-red-500 mt-2">Catatan: {{ $pesanan->pembayaran->catatan }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Aksi --}}
        @if($pesanan->status_pesanan === 'menunggu_pembayaran')
            <div class="bg-yellow-50 border border-yellow-200 rounded-3xl p-6 text-center">
                <i class="fas fa-exclamation-circle text-yellow-500 text-3xl mb-3 block"></i>
                <p class="font-semibold text-yellow-700 mb-1">Segera Lakukan Pembayaran</p>
                <p class="text-sm text-yellow-600 mb-4">Upload bukti transfer/pembayaran untuk memproses pesananmu.</p>
                <a href="{{ route('pembeli.pembayaran.form', $pesanan->id) }}"
                    class="bg-black text-white px-6 py-3 rounded-3xl font-medium hover:bg-gray-800 transition text-sm">
                    <i class="fas fa-upload mr-2"></i> Upload Bukti Bayar
                </a>
            </div>
        @endif
    </div>
</x-pembeli-layout>
