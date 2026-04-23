<x-pembeli-layout>
    <div class="mb-4">
        <a href="{{ route('pembeli.index') }}" class="text-white/70 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Produk
        </a>
    </div>

    <div class="bg-white/90 backdrop-blur-xl rounded-3xl overflow-hidden shadow-xl">
        <div class="flex flex-col md:flex-row">
            {{-- Foto Produk --}}
            <div class="md:w-1/2">
                @php
                    $foto = $produk->foto
                        ? Storage::url($produk->foto)
                        : 'https://picsum.photos/id/' . (($produk->id % 50) + 100) . '/600/700';
                @endphp
                <img src="{{ $foto }}" alt="{{ $produk->nama_produk }}" class="w-full h-80 md:h-full object-cover">
            </div>

            {{-- Detail --}}
            <div class="md:w-1/2 p-8 flex flex-col justify-between">
                <div>
                    <p class="text-sm text-gray-500 capitalize mb-1">{{ $produk->kategori }}</p>
                    <h1 class="text-3xl font-bold mb-2">{{ $produk->nama_produk }}</h1>

                    @if($produk->deskripsi)
                        <p class="text-gray-600 text-sm mb-6">{{ $produk->deskripsi }}</p>
                    @endif

                    {{-- Pilih Varian --}}
                    <form action="{{ route('pembeli.keranjang.tambah') }}" method="POST" class="space-y-5" id="tambah-form">
                        @csrf

                        <div>
                            <p class="text-sm font-semibold mb-3">Pilih Varian (Ukuran & Warna)</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($produk->varians->where('stok', '>', 0) as $varian)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="varian_id" value="{{ $varian->id }}"
                                            class="hidden peer" required
                                            onchange="updateHarga({{ $varian->harga }}, {{ $varian->stok }})">
                                        <span class="peer-checked:bg-[#F3A1BC] peer-checked:text-white peer-checked:border-[#F3A1BC]
                                            px-4 py-2 border border-gray-300 rounded-2xl text-sm font-medium block transition hover:border-pink-300">
                                            {{ $varian->label }} — Rp {{ number_format($varian->harga, 0, ',', '.') }}
                                            <span class="text-xs text-gray-400">(stok: {{ $varian->stok }})</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-red-500 text-sm">Semua varian habis.</p>
                                @endforelse

                                @foreach($produk->varians->where('stok', 0) as $varian)
                                    <span class="px-4 py-2 border border-gray-200 rounded-2xl text-sm text-gray-400 line-through">
                                        {{ $varian->label }} (Habis)
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Harga terpilih --}}
                        <div>
                            <p id="harga-display" class="text-[#F3A1BC] font-bold text-3xl">
                                Rp {{ number_format($produk->harga_min ?? 0, 0, ',', '.') }}
                            </p>
                            <p id="stok-display" class="text-sm text-gray-500 mt-1"></p>
                        </div>

                        {{-- Jumlah --}}
                        <div class="flex items-center gap-4">
                            <p class="text-sm font-medium">Jumlah</p>
                            <div class="flex items-center border border-gray-300 rounded-2xl overflow-hidden">
                                <button type="button" onclick="changeQty(-1)"
                                    class="px-4 py-3 hover:bg-gray-100 font-bold text-lg leading-none">−</button>
                                <input type="number" name="jumlah" id="qty" value="1" min="1"
                                    class="w-14 text-center py-3 border-x border-gray-300 focus:outline-none text-sm font-medium">
                                <button type="button" onclick="changeQty(1)"
                                    class="px-4 py-3 hover:bg-gray-100 font-bold text-lg leading-none">+</button>
                            </div>
                        </div>

                        @auth
                            <button type="submit"
                                class="w-full bg-black text-white py-4 rounded-3xl font-semibold text-lg hover:bg-gray-800 transition">
                                <i class="fas fa-shopping-cart mr-2"></i> Tambah ke Keranjang
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="block w-full bg-black text-white py-4 rounded-3xl font-semibold text-lg hover:bg-gray-800 transition text-center">
                                <i class="fas fa-sign-in-alt mr-2"></i> Masuk untuk Membeli
                            </a>
                        @endauth
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function changeQty(delta) {
            const qty = document.getElementById('qty');
            qty.value = Math.max(1, parseInt(qty.value) + delta);
        }

        function updateHarga(harga, stok) {
            document.getElementById('harga-display').textContent = 'Rp ' + harga.toLocaleString('id-ID');
            document.getElementById('stok-display').textContent = `Stok tersedia: ${stok} pcs`;
        }
    </script>
    @endpush
</x-pembeli-layout>
