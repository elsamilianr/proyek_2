@extends('layouts.karyawan')

@section('title', 'Produk')

@section('content')

<div class="page-header">
    <h1 class="page-title">Produk</h1>
    <div style="display:flex; gap:12px; align-items:center;">
        <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input type="text" placeholder="cari..." id="searchProduk"
                oninput="filterProduk(this.value)">
        </div>
        <a href="{{ route('karyawan.produk.create') }}" class="btn-tambah">
            + Tambah
        </a>
    </div>
</div>

<div class="products-grid" id="produkGrid">
    @forelse($produks ?? [] as $produk)
    <div class="product-card" data-name="{{ strtolower($produk->nama) }}">
        <div class="product-card-image">
            @if($produk->gambar)
                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}">
            @else
                <div style="width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:40px;">📦</div>
            @endif
        </div>
        <div class="product-card-body">
            <div>
                <div class="product-card-name">{{ $produk->nama }}</div>
                <div class="product-card-price">Rp{{ number_format($produk->harga, 0, ',', '.') }}</div>
            </div>
            <div class="product-card-menu" onclick="toggleMenu({{ $produk->id }})">•••</div>
        </div>
        <div id="menu-{{ $produk->id }}" style="display:none; padding: 0 16px 12px; display:none;">
            <a href="{{ route('karyawan.produk.edit', $produk->id) }}"
                style="display:block; padding:8px; background:var(--pink-light); border-radius:8px; text-align:center; font-weight:700; text-decoration:none; color:var(--text-dark); margin-bottom:6px;">
                Edit
            </a>
            <form action="{{ route('karyawan.produk.destroy', $produk->id) }}" method="POST"
                onsubmit="return confirm('Hapus produk ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                    style="width:100%; padding:8px; background:#fee2e2; border:none; border-radius:8px; font-family:'Nunito',sans-serif; font-weight:700; cursor:pointer; color:#dc2626;">
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    {{-- Tampilan dummy jika belum ada data --}}
    @for($i = 0; $i < 6; $i++)
    <div class="product-card">
        <div class="product-card-image">
            <div style="width:100%;height:200px;background:linear-gradient(135deg,#f5f5f5,#e8e8e8);display:flex;align-items:center;justify-content:center;color:#ccc;font-size:40px;">🧥</div>
        </div>
        <div class="product-card-body">
            <div>
                <div class="product-card-name">Basic Slim Fit T-Shirt</div>
                <div class="product-card-price">Rp199.000</div>
            </div>
            <div class="product-card-menu">•••</div>
        </div>
    </div>
    @endfor
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function filterProduk(val) {
    const cards = document.querySelectorAll('#produkGrid .product-card');
    cards.forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = name.includes(val.toLowerCase()) ? '' : 'none';
    });
}

function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);
    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endpush