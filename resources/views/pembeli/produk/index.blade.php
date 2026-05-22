<x-pembeli-layout>
    {{-- Breadcrumb --}}
    <p class="text-sm text-white/70 mb-2">Beranda / <span class="font-semibold text-white">Produk</span></p>
    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-8">PRODUK</h1>

    <div class="flex gap-8">
        {{-- ── Sidebar Filter ── --}}
        <aside class="w-64 hidden lg:block flex-shrink-0">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 sticky top-24">
                <h3 class="font-semibold text-lg mb-6">Filters</h3>

                <form method="GET" action="{{ route('pembeli.index') }}" id="filter-form">
                    {{-- Ukuran --}}
                    <div class="mb-8">
                        <p class="text-sm font-medium mb-3">Ukuran</p>
                        <div class="flex flex-wrap gap-2" id="size-filters">
                            @foreach(['XS','S','M','L','XL','2XL'] as $uk)
                                <button type="button"
                                    onclick="toggleSizeFilter(this, '{{ $uk }}')"
                                    class="size-btn px-4 py-2 border border-gray-300 rounded-2xl text-sm {{ request('ukuran') === $uk ? 'active' : '' }}">
                                    {{ $uk }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="ukuran" id="selected-ukuran" value="{{ request('ukuran') }}">
                    </div>

                    {{-- Ketersediaan --}}
                    <div class="mb-8">
                        <p class="text-sm font-medium mb-3">Ketersediaan</p>
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="radio" name="ketersediaan" value="semua" class="accent-[#F3A1BC]"
                                {{ !request('ketersediaan') || request('ketersediaan') === 'semua' ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            Semua
                        </label>
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="radio" name="ketersediaan" value="tersedia" class="accent-[#F3A1BC]"
                                {{ request('ketersediaan') === 'tersedia' ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            Tersedia
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="ketersediaan" value="habis" class="accent-[#F3A1BC]"
                                {{ request('ketersediaan') === 'habis' ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            Habis
                        </label>
                    </div>

                    {{-- Kategori Accordion --}}
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div onclick="toggleAccordion(this)" class="flex justify-between cursor-pointer items-center accordion-header">
                            <span class="font-medium text-sm">Kategori</span>
                            <span class="toggle-icon text-2xl text-[#F3A1BC]">+</span>
                        </div>
                        <div class="accordion-content {{ request('kategori') ? '' : 'hidden' }} mt-4 space-y-2 pl-1">
                            @foreach(['hijab','gamis','khimar','instan'] as $kat)
                                <label class="flex items-center gap-2 cursor-pointer capitalize text-sm">
                                    <input type="radio" name="kategori" value="{{ $kat }}" class="accent-[#F3A1BC]"
                                        {{ request('kategori') === $kat ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    {{ ucfirst($kat) }}
                                </label>
                            @endforeach
                            @if(request('kategori'))
                                <a href="{{ route('pembeli.index') }}" class="text-xs text-pink-500 hover:underline block mt-2">Reset kategori</a>
                            @endif
                        </div>
                    </div>

                    {{-- Warna Accordion --}}
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div onclick="toggleAccordion(this)" class="flex justify-between cursor-pointer items-center accordion-header">
                            <span class="font-medium text-sm">Warna</span>
                            <span class="toggle-icon text-2xl text-[#F3A1BC]">+</span>
                        </div>

                        <div class="accordion-content hidden mt-4 pl-1">
                            <div class="flex flex-wrap gap-3" id="color-swatches">

                                @php
                                    $colorMap = [

                                        // Indonesia
                                        'hitam' => '#1F1F1F',
                                        'putih' => '#F5F5F5',
                                        'hijau' => '#8BC34A',
                                        'coklat' => '#795548',
                                        'abu' => '#9E9E9E',
                                        'abu-abu' => '#9E9E9E',
                                        'merah' => '#F44336',
                                        'biru' => '#2196F3',
                                        'kuning' => '#FFEB3B',
                                        'ungu' => '#9C27B0',
                                        'oren' => '#FF9800',

                                        // English
                                        'black' => '#1F1F1F',
                                        'white' => '#F5F5F5',
                                        'green' => '#8BC34A',
                                        'brown' => '#795548',
                                        'grey' => '#9E9E9E',
                                        'gray' => '#9E9E9E',
                                        'red' => '#F44336',
                                        'blue' => '#2196F3',
                                        'yellow' => '#FFEB3B',
                                        'purple' => '#9C27B0',
                                        'orange' => '#FF9800',

                                        // tambahan
                                        'pink'  => '#F3A1BC',
                                        'navy'  => '#1E2A44',
                                        'cream' => '#FFF8E7',
                                        'beige' => '#F5E8D3',
                                    ];
                                @endphp

                                @foreach($warnaList as $warna)

                                    @php
                                        $warnaKey = strtolower(trim($warna));
                                        $bgColor = $colorMap[$warnaKey] ?? '#CCCCCC';
                                    @endphp

                                    <button
                                        type="button"
                                        onclick="toggleWarna('{{ $warna }}', this)"
                                        title="{{ ucfirst($warna) }}"
                                        class="w-9 h-9 rounded-2xl shadow-inner border-4 transition flex items-center justify-center
                                        {{ request('warna') === $warna ? 'border-[#F3A1BC]' : 'border-white' }}"
                                        style="background-color: {{ $bgColor }}">

                                        @if($bgColor === '#CCCCCC')
                                            <span class="text-[8px] text-black font-bold">?</span>
                                        @endif

                                    </button>

                                @endforeach

                                <input type="hidden" name="warna" id="selected-warna" value="{{ request('warna') }}">

                            </div>
                        </div>
                    </div>

                    {{-- Harga Accordion --}}
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <div onclick="toggleAccordion(this)" class="flex justify-between cursor-pointer items-center accordion-header">
                            <span class="font-medium text-sm">Harga</span>
                            <span class="toggle-icon text-2xl text-[#F3A1BC]">+</span>
                        </div>
                        <div class="accordion-content hidden mt-4 pl-1 space-y-4">
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Min (Rp)</label>
                                    <input type="number" name="harga_min" value="{{ request('harga_min', 0) }}"
                                        class="w-full border border-gray-300 rounded-2xl px-3 py-2 text-sm">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Max (Rp)</label>
                                    <input type="number" name="harga_max" value="{{ request('harga_max', 500000) }}"
                                        class="w-full border border-gray-300 rounded-2xl px-3 py-2 text-sm">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-black text-white py-2 rounded-2xl text-sm">Terapkan</button>
                        </div>
                    </div>

                    {{-- Preserve search --}}
                    @if(request('cari'))
                        <input type="hidden" name="cari" value="{{ request('cari') }}">
                    @endif
                </form>
            </div>
        </aside>

        {{-- ── Konten Produk ── --}}
        <div class="flex-1 min-w-0">
            {{-- Search + Category chips --}}
            <div class="flex flex-col md:flex-row gap-4 mb-8">
                <form method="GET" action="{{ route('pembeli.index') }}" class="relative flex-1">
                    <input type="text" name="cari" value="{{ request('cari') }}"
                        placeholder="Cari hijab, gamis..."
                        class="w-full bg-white/90 px-6 py-4 rounded-3xl focus:outline-none focus:ring-2 focus:ring-[#F3A1BC]">
                    <button type="submit" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-pink-500">
                        <i class="fas fa-search"></i>
                    </button>
                    @foreach(request()->except(['cari','_token']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                </form>

                <div class="flex gap-2 overflow-x-auto pb-1 flex-shrink-0">
                    @foreach(['' => 'Semua', 'hijab' => 'Hijab', 'gamis' => 'Gamis', 'khimar' => 'Khimar', 'instan' => 'Hijab Instan'] as $val => $label)
                        <a href="{{ route('pembeli.index', array_merge(request()->except('kategori'), $val ? ['kategori' => $val] : [])) }}"
                            class="category-chip whitespace-nowrap px-6 py-3 rounded-3xl text-sm font-medium bg-white/70 text-gray-800 hover:bg-white {{ request('kategori', '') === $val ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Hasil --}}
            <p class="text-white/70 text-sm mb-4">{{ $produk->count() }} produk ditemukan</p>

            @if($produk->isEmpty())
                <div class="bg-white/80 rounded-3xl p-12 text-center">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-xl text-gray-500">Tidak ada produk yang cocok.</p>
                    <a href="{{ route('pembeli.index') }}" class="mt-4 inline-block text-pink-500 hover:underline text-sm">Reset filter</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($produk as $item)
                        @php
                            $hargaMin = $item->varians->min('harga');
                            $stokTotal = $item->varians->sum('stok');
                            $foto = $item->foto
                                ? Storage::url($item->foto)
                                : 'https://picsum.photos/id/' . (($item->id % 50) + 100) . '/320/400';
                        @endphp
                        <a href="{{ route('pembeli.produk.show', $item->slug) }}" class="product-card bg-white rounded-3xl overflow-hidden shadow-sm block">
                            <img src="{{ $foto }}" alt="{{ $item->nama_produk }}" class="w-full h-64 object-cover">
                            <div class="p-4 sm:p-5">
                                <p class="text-xs text-gray-500 capitalize">{{ $item->kategori }}</p>
                                <h3 class="font-semibold text-base mt-1 line-clamp-2">{{ $item->nama_produk }}</h3>
                                <p class="text-[#F3A1BC] font-bold text-xl mt-2">
                                    Rp {{ number_format($hargaMin ?? 0, 0, ',', '.') }}
                                </p>
                                {{-- Ukuran tersedia --}}
                                <div class="flex flex-wrap gap-1 mt-2 mb-3">
                                    @foreach($item->varians->pluck('size')->unique()->take(5) as $ukuran)
                                        <span class="text-[10px] font-medium bg-gray-100 px-2.5 py-1 rounded-2xl">{{ $ukuran }}</span>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="{{ $stokTotal > 0 ? 'text-green-600' : 'text-red-500' }} text-sm font-medium">
                                        {{ $stokTotal > 0 ? 'Tersedia' : 'Habis' }}
                                    </span>
                                    <span class="bg-black text-white px-4 py-2 rounded-2xl text-xs font-medium">
                                        Lihat Detail
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleAccordion(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.toggle-icon');
            content.classList.toggle('hidden');
            icon.textContent = content.classList.contains('hidden') ? '+' : '−';
        }

        function toggleSizeFilter(btn, ukuran) {
            btn.classList.toggle('active');
            document.getElementById('selected-ukuran').value = btn.classList.contains('active') ? ukuran : '';
            document.getElementById('filter-form').submit();
        }

        function toggleWarna(warna, btn) {
            const input = document.getElementById('selected-warna');
            if (input.value === warna) {
                input.value = '';
                btn.style.borderColor = 'white';
            } else {
                input.value = warna;
                document.querySelectorAll('#color-swatches button').forEach(b => b.style.borderColor = 'white');
                btn.style.borderColor = '#F3A1BC';
            }
            document.getElementById('filter-form').submit();
        }
    </script>
    @endpush
</x-pembeli-layout>
