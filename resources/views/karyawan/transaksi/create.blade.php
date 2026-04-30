@extends('layouts.karyawan')

@section('title', 'Transaksi Offline')

@section('content')

<div class="page-header">
    <h1 class="page-title">Transaksi Offline</h1>
    <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
        </svg>
        <input type="text" placeholder="cari produk..." id="searchVarian" oninput="filterVarian(this.value)">
    </div>
</div>

{{-- Form Kasir --}}
<form action="{{ route('karyawan.transaksi.store') }}" method="POST" id="kasirForm">
    @csrf

    {{-- Input nama customer --}}
    <div class="form-group mb-24">
        <label class="form-label">Nama Customer</label>
        <input class="form-input" type="text" name="nama_customer"
            placeholder="masukkan nama customer" required value="{{ old('nama_customer') }}">
    </div>

    {{-- Pilih produk / varian --}}
    <div class="mb-24">
        <div class="form-label" style="font-size:16px;margin-bottom:14px;">Pilih Produk</div>
        <div class="products-grid" id="varianGrid">
            @forelse($varians ?? [] as $varian)
            <div class="product-card" data-name="{{ strtolower($varian->produk->nama ?? '') }}">
                <div class="product-card-image">
                    @if($varian->produk->foto ?? null)
                        <img src="{{ asset('storage/' . $varian->produk->foto) }}" alt="{{ $varian->produk->nama }}">
                    @else
                        <div style="width:100%;height:200px;background:linear-gradient(135deg,#f5f5f5,#e8e8e8);display:flex;align-items:center;justify-content:center;color:#ccc;font-size:40px;">🧥</div>
                    @endif
                </div>
                <div class="product-card-body">
                    <div>
                        <div class="product-card-name">{{ $varian->produk->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--text-gray);margin-bottom:2px;">{{ $varian->warna }} / {{ $varian->ukuran }} | Stok: {{ $varian->stok }}</div>
                        <div class="product-card-price">Rp{{ number_format($varian->produk->harga ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div style="padding:0 14px 14px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <label style="font-size:12px;font-weight:600;">Qty:</label>
                        <input type="number" name="items[{{ $varian->id }}][qty]"
                            min="0" max="{{ $varian->stok }}" value="0"
                            style="width:60px;border:1px solid var(--pink-card);border-radius:8px;padding:4px 8px;font-family:'Nunito',sans-serif;text-align:center;"
                            onchange="hitungTotal()">
                        <input type="hidden" name="items[{{ $varian->id }}][varian_id]" value="{{ $varian->id }}">
                        <input type="hidden" name="items[{{ $varian->id }}][harga]" value="{{ $varian->produk->harga ?? 0 }}">
                    </div>
                </div>
            </div>
            @empty
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
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>

    {{-- Metode Pembayaran --}}
    <div class="mb-24">
        <div class="form-label" style="font-size:16px;margin-bottom:12px;">Metode Pembayaran</div>
        <div class="payment-methods">
            <button type="button" class="payment-btn" onclick="pilihMetode('QRIS', this)">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M18 14h.01M14 18h.01M18 18h.01"/>
                </svg>
                QRIS
            </button>
            <button type="button" class="payment-btn" onclick="pilihMetode('Transfer Bank', this)">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M12 10v11M16 10v11"/>
                </svg>
                BANK
            </button>
            <button type="button" class="payment-btn" onclick="pilihMetode('Tunai', this)">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/>
                    <path d="M6 12h.01M18 12h.01"/>
                </svg>
                CASH
            </button>
        </div>
        <input type="hidden" name="metode_bayar" id="metodePembayaran" value="">
    </div>

    {{-- Footer --}}
    <div class="cart-footer-bar">
        <div class="cart-total-label">
            Total: <span class="cart-total-value" id="totalHarga">Rp0</span>
        </div>
        <button type="button" onclick="submitKasir()" class="btn-checkout">Beli Sekarang</button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function filterVarian(val) {
    document.querySelectorAll('#varianGrid .product-card').forEach(card => {
        card.style.display = (card.dataset.name || '').includes(val.toLowerCase()) ? '' : 'none';
    });
}

function pilihMetode(metode, btn) {
    document.getElementById('metodePembayaran').value = metode;
    document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('input[type=number][name*="[qty]"]').forEach(input => {
        const hargaInput = input.closest('.product-card')?.querySelector('input[name*="[harga]"]');
        const harga = hargaInput ? parseInt(hargaInput.value) : 0;
        const qty = parseInt(input.value) || 0;
        total += harga * qty;
    });
    document.getElementById('totalHarga').textContent = 'Rp' + total.toLocaleString('id-ID');
}

function submitKasir() {
    if (!document.getElementById('metodePembayaran').value) {
        alert('Pilih metode pembayaran terlebih dahulu!');
        return;
    }
    document.getElementById('kasirForm').submit();
}
</script>
@endpush