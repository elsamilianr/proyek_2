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

        {{-- Notifikasi status dari Midtrans Snap --}}
        @if(request('bayar') === 'sukses')
            <div class="bg-green-50 border border-green-200 rounded-3xl p-4 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-green-700 text-sm">Pembayaran berhasil!</p>
                    <p class="text-xs text-green-600">Silakan upload bukti pembayaran di bawah untuk konfirmasi.</p>
                </div>
            </div>
        @elseif(request('bayar') === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-3xl p-4 flex items-center gap-3">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-yellow-700 text-sm">Pembayaran sedang diproses</p>
                    <p class="text-xs text-yellow-600">Upload bukti pembayaran untuk mempercepat verifikasi.</p>
                </div>
            </div>
        @elseif(request('bayar') === 'gagal')
            <div class="bg-red-50 border border-red-200 rounded-3xl p-4 flex items-center gap-3">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-red-700 text-sm">Pembayaran gagal</p>
                    <p class="text-xs text-red-600">Silakan coba lagi atau upload bukti jika sudah membayar.</p>
                </div>
            </div>
        @elseif(request('bayar') === 'ditutup')
            <div class="bg-blue-50 border border-blue-200 rounded-3xl p-4 flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                <div>
                    <p class="font-semibold text-blue-700 text-sm">Popup pembayaran ditutup</p>
                    <p class="text-xs text-blue-600">Jika sudah membayar, upload bukti di bawah untuk konfirmasi.</p>
                </div>
            </div>
        @endif
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
                            : null;
                    @endphp
                    <div class="py-4 flex gap-4 items-center">
                        @if($foto)
                            <img src="{{ $foto }}" class="w-16 h-16 rounded-2xl object-cover">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-pink-50 flex items-center justify-center text-2xl">📦</div>
                        @endif
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
                        <p class="text-sm font-medium text-gray-700">Metode:
                            @php
                                $labelMetode = match($pesanan->pembayaran->metode) {
                                    'qris'        => 'QRIS',
                                    'gopay'       => 'GoPay',
                                    'credit_card' => 'Kartu Kredit',
                                    'transfer'    => 'Transfer Bank',
                                    'cash'        => 'Bayar di Tempat (Cash)',
                                    'midtrans'    => 'Midtrans',
                                    default       => ucwords(str_replace('_', ' ', $pesanan->pembayaran->metode)),
                                };
                            @endphp
                            {{ $labelMetode }}
                        </p>
                        <p class="text-sm text-gray-500">Status: <span class="font-semibold">{{ $pesanan->pembayaran->label_status }}</span></p>
                        @if($pesanan->pembayaran->catatan)
                            <p class="text-xs text-red-500 mt-2">Catatan: {{ $pesanan->pembayaran->catatan }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Form upload bukti: muncul jika metode bukan cash, belum selesai/dibatalkan, dan belum diproses --}}
        @if(
            $pesanan->pembayaran &&
            $pesanan->pembayaran->metode !== 'cash' &&
            !in_array($pesanan->status_pesanan, ['diproses', 'selesai', 'dibatalkan'])
        )
            <div id="upload-bukti" class="bg-white/90 rounded-3xl p-6 shadow-sm scroll-mt-8">
                <h2 class="text-lg font-bold mb-4">
                    <i class="fas fa-receipt text-pink-400 mr-2"></i>
                    {{ $pesanan->pembayaran->bukti_pembayaran ? 'Perbarui Bukti Pembayaran' : 'Upload Bukti Pembayaran' }}
                </h2>
                <p class="text-sm text-gray-500 mb-4">
                    {{ $pesanan->pembayaran->bukti_pembayaran
                        ? 'Sudah ada bukti yang dikirim. Kamu bisa mengirim ulang jika ada kesalahan.'
                        : 'Selesaikan pembayaranmu lalu upload bukti di sini.' }}
                </p>

                @if($pesanan->pembayaran->bukti_pembayaran)
                    <div class="mb-4">
                        <p class="text-xs text-gray-400 mb-2">Bukti saat ini:</p>
                        <img src="{{ Storage::url($pesanan->pembayaran->bukti_pembayaran) }}"
                            class="w-32 h-32 rounded-2xl object-cover border border-pink-200" alt="Bukti Pembayaran">
                    </div>
                @endif

                <form id="formUploadBukti" action="{{ route('pembeli.pesanan.bukti', $pesanan->id) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="metode" value="{{ $pesanan->pembayaran->metode }}">

                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-5 text-center
                        hover:border-pink-300 transition cursor-pointer mb-4"
                        id="dropzoneBukti">
                        <div id="show-bukti-placeholder">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Klik untuk pilih gambar</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — Maks 5MB</p>
                        </div>
                        <img id="show-bukti-preview" class="hidden mx-auto max-h-40 rounded-2xl object-contain" alt="Preview">
                    </div>
                    <input type="file" id="bukti-show-input" name="bukti_pembayaran"
                        accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden"
                        onchange="previewShowBukti(event)">
                    <p id="bukti-error" class="text-red-500 text-xs mb-3 hidden">Pilih gambar terlebih dahulu.</p>

                    <button type="button" onclick="submitBukti()"
                        class="w-full bg-black text-white py-3 rounded-3xl font-semibold text-sm hover:bg-gray-800 transition">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    // Klik dropzone → buka file picker
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('dropzoneBukti');
        if (dropzone) {
            dropzone.addEventListener('click', function () {
                document.getElementById('bukti-show-input').click();
            });
        }

        // Auto-scroll ke form upload jika redirect dari Snap Midtrans
        const params = new URLSearchParams(window.location.search);
        if (params.has('bayar')) {
            const target = document.getElementById('upload-bukti');
            if (target) {
                setTimeout(() => {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 400);
            }
        }
    });

    function previewShowBukti(e) {
        const file = e.target.files[0];
        if (!file) return;
        document.getElementById('bukti-error').classList.add('hidden');
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('show-bukti-placeholder').classList.add('hidden');
            const img = document.getElementById('show-bukti-preview');
            img.src = ev.target.result;
            img.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function submitBukti() {
        const input = document.getElementById('bukti-show-input');
        if (!input.files || input.files.length === 0) {
            document.getElementById('bukti-error').classList.remove('hidden');
            document.getElementById('dropzoneBukti').classList.add('border-red-400');
            return;
        }
        document.getElementById('formUploadBukti').submit();
    }
    </script>
    @endpush
</x-pembeli-layout>