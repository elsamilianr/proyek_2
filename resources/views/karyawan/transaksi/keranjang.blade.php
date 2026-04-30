@extends('layouts.karyawan')

@section('title', 'Keranjang')

@section('content')

<div class="page-header">
    <h1 class="page-title">Keranjang</h1>
    <div style="display:flex;gap:12px;align-items:center;">
        <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input type="text" placeholder="cari...">
        </div>
        <a href="{{ route('karyawan.transaksi.index') }}" class="btn-tambah">+ Tambah</a>
    </div>
</div>

<div class="products-grid pb-80" id="keranjangGrid">
    @forelse($keranjang ?? [] as $key => $item)
    <div class="cart-card">
        <div class="cart-card-image">
            @if($item['gambar'] ?? null)
                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama'] }}">
            @else
                <div style="width:100%;height:100%;background:linear-gradient(135deg,#f5f5f5,#e8e8e8);display:flex;align-items:center;justify-content:center;font-size:40px;">🧥</div>
            @endif
        </div>
        <div class="cart-card-body">
            <div class="cart-card-menu">
                <div class="cart-card-name">{{ $item['nama'] }}</div>
                <span style="color:var(--text-gray);font-size:18px;cursor:pointer;">•••</span>
            </div>
            <div class="cart-card-variant">{{ $item['warna'] }}, {{ $item['size'] }}</div>
            <div class="cart-card-footer">
                <div class="cart-card-price">Rp{{ number_format($item['harga'], 0, ',', '.') }}</div>
                <div class="qty-control">
                    <form action="{{ route('karyawan.transaksi.updateQty', $key) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="kurang">
                        <button type="submit" class="qty-btn">-</button>
                    </form>
                    <span class="qty-value">{{ $item['qty'] }}</span>
                    <form action="{{ route('karyawan.transaksi.updateQty', $key) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="action" value="tambah">
                        <button type="submit" class="qty-btn">+</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    @for($i = 0; $i < 3; $i++)
    <div class="cart-card">
        <div class="cart-card-image">
            <div style="width:100%;height:180px;background:linear-gradient(135deg,#f5f5f5,#e8e8e8);display:flex;align-items:center;justify-content:center;font-size:40px;">🧥</div>
        </div>
        <div class="cart-card-body">
            <div class="cart-card-menu">
                <div class="cart-card-name">Basic Slim Fit T-Shirt</div>
                <span style="color:var(--text-gray);font-size:18px;">•••</span>
            </div>
            <div class="cart-card-variant">White, L</div>
            <div class="cart-card-footer">
                <div class="cart-card-price">Rp199.000</div>
                <div class="qty-control">
                    <button class="qty-btn">-</button>
                    <span class="qty-value">1</span>
                    <button class="qty-btn">+</button>
                </div>
            </div>
        </div>
    </div>
    @endfor
    @endforelse
</div>

{{-- FOOTER BAR --}}
<div class="cart-footer-bar">
    <div class="cart-total-label">
        Total: <span class="cart-total-value">Rp{{ number_format($total ?? 199000, 0, ',', '.') }}</span>
    </div>
    <a href="{{ route('karyawan.transaksi.checkout') }}" class="btn-checkout">Checkout</a>
</div>

@endsection