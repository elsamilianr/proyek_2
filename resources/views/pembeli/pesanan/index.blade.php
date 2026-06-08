<x-pembeli-layout>
    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-8">Pesanan Saya</h1>

    @if($pesanans->isEmpty())
        <div class="bg-white/90 rounded-3xl p-12 text-center shadow-lg">
            <i class="fas fa-box-open text-6xl text-gray-200 mb-6 block"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-2">Belum Ada Pesanan</h2>
            <p class="text-gray-400 mb-6">Yuk mulai belanja dan buat pesanan pertamamu!</p>
            <a href="{{ route('pembeli.index') }}" class="bg-black text-white px-8 py-4 rounded-3xl font-medium hover:bg-gray-800 transition">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($pesanans as $pesanan)
                @php
                    $statusColor = match($pesanan->status_pesanan) {
                        'menunggu_pembayaran'  => 'bg-yellow-100 text-yellow-700',
                        'menunggu_verifikasi' => 'bg-blue-100 text-blue-700',
                        'diproses'            => 'bg-purple-100 text-purple-700',
                        'selesai'             => 'bg-green-100 text-green-700',
                        'dibatalkan'          => 'bg-red-100 text-red-700',
                        default               => 'bg-gray-100 text-gray-600',
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
                <a href="{{ route('pembeli.pesanan.show', $pesanan->id) }}"
                    class="bg-white/90 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition block">

                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="font-bold text-base">{{ $pesanan->kode_pesanan }}</p>
                            <span class="text-xs px-3 py-1 rounded-full font-medium {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ $pesanan->tanggal ? $pesanan->tanggal->format('d M Y') : '-' }} ·
                            {{ $pesanan->details->count() }} item
                        </p>
                        <div class="flex gap-2 mt-2">
                            @foreach($pesanan->details->take(3) as $detail)
                                @php
                                    $foto = $detail->varian?->produk?->foto
                                        ? Storage::url($detail->varian->produk->foto)
                                        : null;
                                @endphp
                                @if($foto)
                                    <img src="{{ $foto }}" class="w-10 h-10 rounded-xl object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center text-lg">📦</div>
                                @endif
                            @endforeach
                            @if($pesanan->details->count() > 3)
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-xs text-gray-500 font-medium">
                                    +{{ $pesanan->details->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-lg font-bold text-[#F3A1BC]">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </p>
                        @if($pesanan->status_pesanan === 'menunggu_pembayaran')
                            <span class="text-xs text-pink-500 font-medium mt-1 block">
                                <i class="fas fa-exclamation-circle mr-1"></i> Upload bukti bayar
                            </span>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Lihat Detail <i class="fas fa-chevron-right ml-1"></i></p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-pembeli-layout>