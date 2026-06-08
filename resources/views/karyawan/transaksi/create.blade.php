@extends('layouts.karyawan')

@section('title', 'Transaksi Offline')

@section('content')

{{-- Header --}}
<div class="page-header">
    <h1 class="page-title">Transaksi Offline</h1>
    <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
        </svg>
        <input type="text" placeholder="cari produk..."
               id="searchVarian" oninput="cariProduk(this.value)">
    </div>
</div>

{{-- Flash error dari store() --}}
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;
            padding:12px 16px;margin-bottom:16px;color:#dc2626;font-weight:600;font-size:14px;">
    ⚠ {{ session('error') }}
</div>
@endif

<form action="{{ route('karyawan.transaksi.store') }}" method="POST" id="kasirForm">
@csrf
<input type="hidden" name="metode_bayar"      id="metodePembayaran">
<input type="hidden" name="midtrans_order_id" id="midtransOrderId">

{{-- Container khusus untuk hidden input items — di luar grid agar tidak kena innerHTML reset --}}
<div id="itemsContainer" style="display:none;"></div>

{{-- Hasil Pencarian --}}
<div class="mb-24">
    <div class="form-label" style="font-size:15px;margin-bottom:12px;">Hasil Pencarian</div>
    <div class="products-grid" id="searchResults">
        <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">
            Ketik nama produk
        </div>
    </div>
</div>

{{-- Produk yang Akan Dibeli --}}
<div class="mb-24">
    <div class="form-label" style="font-size:15px;margin-bottom:12px;">Produk yang Akan Dibeli</div>
    <div class="products-grid" id="selectedProducts">
        <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">
            Belum ada produk dipilih
        </div>
    </div>
</div>

{{-- Metode Pembayaran --}}
<div class="mb-24">
    <div class="form-label" style="font-size:15px;margin-bottom:12px;">Metode Pembayaran</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
        <button type="button" class="payment-btn" onclick="pilihMetode('qris','other_qris',this)">QRIS</button>
        <button type="button" class="payment-btn" onclick="pilihMetode('transfer',null,this)">Transfer</button>
        <button type="button" class="payment-btn" onclick="pilihMetode('cash',null,this)">Cash</button>
    </div>
    {{-- Opsi tambahan Midtrans --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:10px;">
        <button type="button" class="payment-btn" onclick="pilihMetode('gopay','gopay',this)"
                style="font-size:13px;">💚 GoPay <span style="font-size:10px;font-weight:400;display:block;color:#9ca3af;">via Midtrans</span></button>
        <button type="button" class="payment-btn" onclick="pilihMetode('credit_card','credit_card',this)"
                style="font-size:13px;">💳 Kartu Kredit/Debit <span style="font-size:10px;font-weight:400;display:block;color:#9ca3af;">via Midtrans</span></button>
    </div>
</div>

</form>

{{-- Footer sticky: Total + Beli --}}
<div style="position:fixed;bottom:0;left:230px;right:0;background:#fff;
            border-top:2px solid var(--pink-card);padding:14px 32px;
            display:flex;align-items:center;justify-content:space-between;z-index:100;">
    <span style="font-size:16px;font-weight:700;color:var(--text-dark);">
        Total: <span id="totalHarga">Rp0</span>
    </span>
    <button type="button" onclick="submitKasir()" class="btn-checkout"
            id="btnBeli" style="border-radius:12px;padding:12px 32px;">
        Beli Sekarang
    </button>
</div>

{{-- Spacer agar konten tidak tertutup footer --}}
<div style="height:80px;"></div>

{{-- Loading overlay Midtrans --}}
<div id="midtransOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);
     z-index:9998;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px 40px;text-align:center;min-width:220px;">
        <div style="font-size:18px;font-weight:700;margin-bottom:8px;">Memproses Pembayaran</div>
        <div style="color:#888;font-size:14px;">Mohon tunggu sebentar…</div>
    </div>
</div>

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
let cart = {}, hasilSearch = {}, searchTimeout = null, activeRequest = null;
const MIDTRANS_METHODS = ['qris', 'gopay', 'credit_card'];
let selectedMetode = null, selectedChannel = null;

/* ─── Gambar produk ─── */
function getImage(foto) {
    if (foto) return `<img src="/storage/${foto}" style="width:100%;height:100%;object-fit:cover;">`;
    return `<div style="width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;
            justify-content:center;color:#ccc;font-size:36px;">📦</div>`;
}

/* ─── Cari produk via AJAX ─── */
function cariProduk(keyword) {
    const results = document.getElementById('searchResults');
    clearTimeout(searchTimeout);
    if (activeRequest) { activeRequest.abort(); activeRequest = null; }

    if (!keyword.trim()) {
        results.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">Ketik nama produk</div>`;
        return;
    }

    results.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">Mencari...</div>`;
    searchTimeout = setTimeout(() => {
        activeRequest = new AbortController();
        fetch(`/karyawan/transaksi/cari-varian?q=${encodeURIComponent(keyword)}&_=${Date.now()}`,
            { cache: 'no-store', signal: activeRequest.signal })
        .then(r => r.json())
        .then(data => {
            activeRequest = null;
            hasilSearch = {};
            if (!data.length) {
                results.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">Produk tidak ditemukan</div>`;
                return;
            }
            results.innerHTML = '';
            data.forEach(item => {
                hasilSearch[item.id] = item;
                const div = document.createElement('div');
                div.className = 'product-card';
                div.innerHTML = `
                    <div class="product-card-image">${getImage(item.produk?.foto)}</div>
                    <div class="product-card-body" style="flex-direction:column;align-items:stretch;gap:8px;">
                        <div>
                            <div class="product-card-name">${item.produk?.nama_produk ?? '-'}</div>
                            <div style="font-size:11px;color:var(--text-gray);">${item.warna||'-'} / ${item.size||'-'}</div>
                            <div class="product-card-price" style="font-size:14px;">Rp${Number(item.harga).toLocaleString('id-ID')}</div>
                        </div>
                        <button type="button" class="btn-tambah"
                                style="width:100%;justify-content:center;font-size:13px;padding:8px;">
                            + Tambah
                        </button>
                    </div>`;
                div.querySelector('.btn-tambah').addEventListener('click', () => tambahProduk(item.id));
                results.appendChild(div);
            });
        })
        .catch(err => {
            if (err.name !== 'AbortError')
                results.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#f87171;">Terjadi error, coba lagi.</div>`;
        });
    }, 300);
}

/* ─── Kelola keranjang ─── */
function tambahProduk(id) {
    const item = hasilSearch[id];
    if (!item) return;
    cart[id] ? cart[id].qty++ : (cart[id] = { ...item, qty: 1 });
    renderCart();
}

function ubahQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const c         = document.getElementById('selectedProducts');
    const container = document.getElementById('itemsContainer');

    // Hapus semua hidden inputs lama
    container.innerHTML = '';

    const keys = Object.keys(cart);
    if (!keys.length) {
        c.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:#aaa;">Belum ada produk dipilih</div>`;
        hitungTotal();
        return;
    }

    // Render kartu produk menggunakan DOM API (bukan innerHTML) agar tidak ada konflik
    c.innerHTML = '';
    keys.forEach(id => {
        const item = cart[id];

        const div = document.createElement('div');
        div.className = 'product-card';
        div.innerHTML = `
            <div class="product-card-image">${getImage(item.produk?.foto)}</div>
            <div class="product-card-body" style="flex-direction:column;align-items:stretch;gap:8px;">
                <div>
                    <div class="product-card-name">${item.produk?.nama_produk ?? '-'}</div>
                    <div style="font-size:11px;color:var(--text-gray);">${item.warna||'-'} / ${item.size||'-'}</div>
                    <div class="product-card-price" style="font-size:14px;">Rp${Number(item.harga).toLocaleString('id-ID')}</div>
                </div>
                <div style="display:flex;align-items:center;justify-content:center;gap:10px;">
                    <button type="button" data-action="minus" data-id="${item.id}"
                        style="background:var(--pink-card);border:none;border-radius:8px;width:28px;height:28px;font-weight:700;cursor:pointer;font-size:16px;">−</button>
                    <span style="font-weight:700;min-width:24px;text-align:center;">${item.qty}</span>
                    <button type="button" data-action="plus" data-id="${item.id}"
                        style="background:var(--pink-card);border:none;border-radius:8px;width:28px;height:28px;font-weight:700;cursor:pointer;font-size:16px;">+</button>
                </div>
            </div>`;

        // Event listener untuk tombol +/-
        div.querySelectorAll('button[data-action]').forEach(btn => {
            btn.addEventListener('click', () => {
                const delta = btn.dataset.action === 'plus' ? 1 : -1;
                ubahQty(btn.dataset.id, delta);
            });
        });

        c.appendChild(div);

        // Buat hidden inputs di #itemsContainer (bagian dari form, tapi terpisah dari grid)
        const inputVarian = document.createElement('input');
        inputVarian.type  = 'hidden';
        inputVarian.name  = `items[${item.id}][varian_id]`;
        inputVarian.value = item.id;
        container.appendChild(inputVarian);

        const inputJumlah = document.createElement('input');
        inputJumlah.type  = 'hidden';
        inputJumlah.name  = `items[${item.id}][jumlah]`;
        inputJumlah.value = item.qty;
        container.appendChild(inputJumlah);
    });

    hitungTotal();
}

function hitungTotal() {
    let t = 0;
    Object.values(cart).forEach(i => t += i.harga * i.qty);
    document.getElementById('totalHarga').textContent = 'Rp' + t.toLocaleString('id-ID');
}

/* ─── Pilih metode pembayaran ─── */
function pilihMetode(metode, channel, btn) {
    selectedMetode  = metode;
    selectedChannel = channel;
    document.getElementById('metodePembayaran').value = metode;
    document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

/* ─── Submit transaksi ─── */
function submitKasir() {
    if (!Object.keys(cart).length) {
        alert('Pilih produk terlebih dahulu.');
        return;
    }
    if (!selectedMetode) {
        alert('Pilih metode pembayaran terlebih dahulu.');
        return;
    }

    if (!MIDTRANS_METHODS.includes(selectedMetode)) {
        // Cash atau Transfer → submit form biasa
        document.getElementById('kasirForm').submit();
        return;
    }

    bayarViaMidtrans();
}

/* ─── Bayar via Midtrans Snap ─── */
async function bayarViaMidtrans() {
    const btn       = document.getElementById('btnBeli');
    const overlay   = document.getElementById('midtransOverlay');
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const items     = Object.values(cart).map(i => ({ varian_id: i.id, jumlah: i.qty }));
    const body      = { _token: csrfToken, items, metode_bayar: selectedMetode };

    overlay.style.display = 'flex';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route("karyawan.transaksi.midtrans-token") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body:    JSON.stringify(body),
        });

        if (!res.ok) {
            const d = await res.json();
            throw new Error(d.error ?? 'Server error');
        }

        const data = await res.json();
        overlay.style.display = 'none';

        const opts = {
            onSuccess: () => {
                document.getElementById('midtransOrderId').value = data.temp_order_id;
                document.getElementById('kasirForm').submit();
            },
            onPending: () => {
                document.getElementById('midtransOrderId').value = data.temp_order_id;
                document.getElementById('kasirForm').submit();
            },
            onError:  (r) => { alert('Pembayaran gagal: ' + (r.status_message ?? '-')); btn.disabled = false; },
            onClose:  ()  => { btn.disabled = false; },
        };

        if (selectedChannel) opts.enabledPayments = [selectedChannel];
        window.snap.pay(data.snap_token, opts);

    } catch (err) {
        overlay.style.display = 'none';
        btn.disabled = false;
        alert('Gagal: ' + err.message);
    }
}
</script>
@endpush

@endsection