@extends('layouts.karyawan')

@section('title', 'Checkout')

@section('content')

<div class="checkout-page pb-80">
    <h2 class="checkout-title">Checkout</h2>

    <form action="{{ route('karyawan.transaksi.proses') }}" method="POST" id="checkoutForm">
        @csrf

        <div class="form-group mb-24">
            <label class="form-label" style="font-size:16px;">Nama Customer</label>
            <input class="form-input" type="text" name="nama_customer"
                placeholder="masukkan nama customer" required
                value="{{ old('nama_customer') }}">
        </div>

        <div class="mb-24">
            <div class="form-label" style="font-size:16px;margin-bottom:14px;">Produk</div>
            <div class="checkout-products-grid">
                @forelse($keranjang ?? [] as $key => $item)
                <div class="checkout-product-card">
                    <div class="checkout-product-img">
                        @if($item['gambar'] ?? null)
                            <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama'] }}">
                        @else
                            <div style="width:100%;height:100%;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:30px;">🧥</div>
                        @endif
                    </div>
                    <div class="checkout-product-info">
                        <div class="checkout-product-name">{{ $item['nama'] }}</div>
                        <div class="checkout-product-variant">{{ $item['warna'] }}, {{ $item['size'] }}</div>
                        <div class="checkout-product-price">Rp{{ number_format($item['harga'], 0, ',', '.') }}</div>
                        <div class="checkout-qty-control">
                            <button type="button" class="checkout-qty-btn"
                                onclick="updateQty('{{ $key }}', -1)">-</button>
                            <span class="checkout-qty-value" id="qty-{{ $key }}">{{ $item['qty'] }}</span>
                            <button type="button" class="checkout-qty-btn"
                                onclick="updateQty('{{ $key }}', 1)">+</button>
                        </div>
                    </div>
                </div>
                @empty
                @for($i = 0; $i < 4; $i++)
                <div class="checkout-product-card">
                    <div class="checkout-product-img">
                        <div style="width:100%;height:100%;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:30px;">🧥</div>
                    </div>
                    <div class="checkout-product-info">
                        <div class="checkout-product-name">Basic Slim Fit T-Shirt</div>
                        <div class="checkout-product-variant">White, L</div>
                        <div class="checkout-product-price">Rp199.000</div>
                        <div class="checkout-qty-control">
                            <button type="button" class="checkout-qty-btn">-</button>
                            <span class="checkout-qty-value">1</span>
                            <button type="button" class="checkout-qty-btn">+</button>
                        </div>
                    </div>
                </div>
                @endfor
                @endforelse
            </div>
        </div>

        <div class="mb-24">
            <div class="form-label" style="font-size:16px;margin-bottom:12px;">Metode Pembayaran</div>
            <div class="payment-methods">
                <button type="button" class="payment-btn" onclick="pilihMetode('qris', this)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M18 14h.01M14 18h.01M18 18h.01"/>
                    </svg>
                    QRIS
                </button>
                <button type="button" class="payment-btn" onclick="pilihMetode('gopay', this)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/>
                    </svg>
                    GoPay
                </button>
                <button type="button" class="payment-btn" onclick="pilihMetode('cash', this)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/>
                        <path d="M6 12h.01M18 12h.01"/>
                    </svg>
                    CASH
                </button>
            </div>
            <input type="hidden" name="metode_bayar" id="metodePembayaran" value="">
        </div>

    </form>
</div>

{{-- FOOTER --}}
<div class="checkout-footer">
    <div class="cart-total-label">
        Total: <span class="cart-total-value">Rp{{ number_format($total ?? 199000, 0, ',', '.') }}</span>
    </div>
    <button type="button" onclick="submitCheckout()" class="btn-checkout">Beli Sekarang</button>
</div>

@endsection

@push('scripts')
<script>
function pilihMetode(metode, btn) {
    document.getElementById('metodePembayaran').value = metode;
    document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function updateQty(key, delta) {
    const el = document.getElementById('qty-' + key);
    if (!el) return;
    let qty = parseInt(el.textContent) + delta;
    if (qty < 1) qty = 1;
    el.textContent = qty;
}

function submitCheckout() {
    const metode = document.getElementById('metodePembayaran').value;
    if (!metode) {
        alert('Pilih metode pembayaran terlebih dahulu!');
        return;
    }
    document.getElementById('checkoutForm').submit();
}
</script>
@endpush