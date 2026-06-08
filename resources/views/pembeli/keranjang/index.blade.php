<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.index') }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Lanjut Belanja
        </a>
    </div>
    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-8">Keranjang Saya</h1>

    @if(!$keranjang || $keranjang->details->isEmpty())
        <div class="bg-white/90 rounded-3xl p-12 text-center shadow-lg">
            <i class="fas fa-shopping-cart text-6xl text-gray-200 mb-6 block"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-2">Keranjang Kosong</h2>
            <p class="text-gray-400 mb-6">Belum ada produk di keranjang kamu.</p>
            <a href="{{ route('pembeli.index') }}" class="bg-black text-white px-8 py-4 rounded-3xl font-medium hover:bg-gray-800 transition">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Daftar Item --}}
            <div class="flex-1 space-y-4">
                @php $subtotalTotal = 0; @endphp
                @foreach($keranjang->details as $detail)
                    @php
                        $subtotal = $detail->jumlah * ($detail->varian->harga ?? 0);
                        $subtotalTotal += $subtotal;
                        $foto = $detail->varian->produk->foto
                            ? Storage::url($detail->varian->produk->foto)
                            : null;
                    @endphp
                    <div class="bg-white/90 rounded-3xl p-5 flex gap-4 shadow-sm">
                        @if($foto)
                            <img src="{{ $foto }}" alt="{{ $detail->varian->produk->nama_produk }}"
                                class="w-20 h-20 object-cover rounded-2xl flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-pink-50 flex items-center justify-center text-3xl flex-shrink-0">📦</div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-base">{{ $detail->varian->produk->nama_produk }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $detail->varian->label }}</p>
                            <p class="text-[#F3A1BC] font-bold mt-1">
                                Rp {{ number_format($detail->varian->harga ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex flex-col items-end justify-between gap-3">
                            <p class="font-bold text-base">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>

                            {{-- Update qty --}}
                            <form action="{{ route('pembeli.keranjang.update', $detail->id) }}" method="POST" class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <button type="button" onclick="changeQtyItem(this, -1)"
                                    class="w-8 h-8 border rounded-xl flex items-center justify-center hover:bg-gray-100 font-bold">−</button>
                                <input type="number" name="jumlah" value="{{ $detail->jumlah }}" min="1"
                                    class="w-12 text-center border rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-pink-300 appearance-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [-moz-appearance:textfield]"
                                    style="height:32px; line-height:32px; padding:0; vertical-align:middle;"
                                    onchange="this.form.submit()">
                                <button type="button" onclick="changeQtyItem(this, 1)"
                                    class="w-8 h-8 border rounded-xl flex items-center justify-center hover:bg-gray-100 font-bold">+</button>
                            </form>

                            {{-- Hapus --}}
                            <form action="{{ route('pembeli.keranjang.hapus', $detail->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                    onclick="return confirm('Hapus item ini?')">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Ringkasan --}}
            <div class="lg:w-80 flex-shrink-0">
                <div class="bg-white/90 rounded-3xl p-6 shadow-lg sticky top-24">
                    <h2 class="text-xl font-bold mb-6">Ringkasan Pesanan</h2>

                    <div class="space-y-3 text-sm text-gray-600 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal ({{ $keranjang->details->sum('jumlah') }} item)</span>
                            <span class="font-semibold">Rp {{ number_format($subtotalTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>Ongkos kirim</span>
                            <span>Ditentukan saat checkout</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>Rp {{ number_format($subtotalTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('pembeli.checkout') }}"
                        class="block w-full bg-black text-white py-4 rounded-3xl text-center font-semibold text-base hover:bg-gray-800 transition">
                        Lanjut Checkout <i class="fas fa-arrow-right ml-2"></i>
                    </a>

                    <a href="{{ route('pembeli.index') }}" class="block text-center text-sm text-gray-400 mt-4 hover:text-pink-500">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        function changeQtyItem(btn, delta) {
            const form = btn.closest('form');
            const input = form.querySelector('input[name="jumlah"]');
            input.value = Math.max(1, parseInt(input.value) + delta);
            form.submit();
        }
    </script>
    @endpush
</x-pembeli-layout>