@extends('layouts.karyawan')

@section('title', 'Transaksi Offline')

@section('content')

<div style="padding-bottom:180px;">

<div class="page-header">
    <h1 class="page-title">Transaksi Offline</h1>

    <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
        </svg>

        <input type="text"
               placeholder="cari produk..."
               id="searchVarian"
               oninput="cariProduk(this.value)">
    </div>
</div>

<form action="{{ route('karyawan.transaksi.store') }}" method="POST" id="kasirForm">
@csrf

{{-- hasil pencarian --}}
<div class="mb-24">
    <div class="form-label" style="font-size:16px;margin-bottom:14px;">
        Hasil Pencarian
    </div>

    <div class="products-grid" id="searchResults">
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
            Ketik nama produk
        </div>
    </div>
</div>

{{-- keranjang --}}
<div class="mb-24">
    <div class="form-label" style="font-size:16px;margin-bottom:14px;">
        Produk yang Akan Dibeli
    </div>

    <div class="products-grid" id="selectedProducts">
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
            Belum ada produk dipilih
        </div>
    </div>
</div>

{{-- pembayaran --}}
<div class="mb-24">
    <div class="form-label" style="font-size:16px;margin-bottom:12px;">
        Metode Pembayaran
    </div>

    <div class="payment-methods" style="margin-bottom:80px;">
        <button type="button" class="payment-btn" onclick="pilihMetode('qris',this)">QRIS</button>
        <button type="button" class="payment-btn" onclick="pilihMetode('transfer',this)">Transfer</button>
        <button type="button" class="payment-btn" onclick="pilihMetode('cash',this)">Cash</button>
    </div>

    <input type="hidden" name="metode_bayar" id="metodePembayaran">
</div>

<div class="cart-footer-bar">
    <div class="cart-total-label">
        Total:
        <span class="cart-total-value" id="totalHarga">Rp0</span>
    </div>

    <button type="button" onclick="submitKasir()" class="btn-checkout">
        Beli Sekarang
    </button>
</div>

</form>
</div>

@endsection

@push('scripts')
<script>
let cart = {};
let hasilSearch = {};
let searchTimeout  = null;   // untuk debounce
let activeRequest  = null;   // AbortController request aktif

function getImage(foto){
    if(foto){
        return `<img src="/storage/${foto}" style="width:100%;height:100%;object-fit:cover;">`;
    }

    return `
    <div style="width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:40px;">
        📦
    </div>`;
}

// 🔍 SEARCH KE BACKEND (debounce 300ms + batalkan request lama)
function cariProduk(keyword){
    const results = document.getElementById('searchResults');

    // Batalkan debounce sebelumnya
    clearTimeout(searchTimeout);

    // Batalkan request HTTP yang sedang berjalan
    if(activeRequest){
        activeRequest.abort();
        activeRequest = null;
    }

    if(!keyword){
        results.innerHTML = `
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
            Ketik nama produk
        </div>`;
        return;
    }

    // Tampilkan loading sementara menunggu debounce selesai
    results.innerHTML = `
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
        Mencari...
    </div>`;

    // Tunda 300ms — jika ada ketukan lagi, timeout ini dibatalkan
    searchTimeout = setTimeout(() => {
        activeRequest = new AbortController();

        fetch(
            `/karyawan/transaksi/cari-varian?q=${encodeURIComponent(keyword)}&_=${Date.now()}`,
            { cache: 'no-store', signal: activeRequest.signal }
        )
        .then(res => res.json())
        .then(data => {
            activeRequest = null;
            hasilSearch   = {}; // reset cache lokal

            if(!data.length){
                results.innerHTML = `
                <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
                    Produk tidak ditemukan
                </div>`;
                return;
            }

            results.innerHTML = '';

            data.forEach(item => {
                hasilSearch[item.id] = item;

                results.innerHTML += `
                <div class="product-card">
                    <div class="product-card-image">
                        ${getImage(item.produk?.foto)}
                    </div>

                    <div class="product-card-body" style="flex-direction:column;align-items:stretch;gap:10px;">
                        <div>
                            <div class="product-card-name">${item.produk?.nama_produk ?? '-'}</div>
                            <div style="font-size:12px;color:var(--text-gray);margin-bottom:4px;">
                                ${item.warna || '-'} / ${item.size || '-'}
                            </div>
                            <div class="product-card-price">
                                Rp${Number(item.harga).toLocaleString('id-ID')}
                            </div>
                        </div>

                        <button
                            type="button"
                            onclick="tambahProduk(${item.id})"
                            class="btn-tambah"
                            style="width:100%;justify-content:center;">
                            + Tambah
                        </button>
                    </div>
                </div>`;
            });
        })
        .catch(err => {
            if(err.name === 'AbortError') return; // request dibatalkan — abaikan
            console.error(err);
            results.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#f87171;">
                Terjadi error, coba lagi.
            </div>`;
        });
    }, 300);
}

// ➕ TAMBAH KE CART
function tambahProduk(id){
    const item = hasilSearch[id];

    if(!item){
        console.error('Item tidak ditemukan');
        return;
    }

    if(cart[id]){
        cart[id].qty++;
    }else{
        cart[id] = {...item, qty:1};
    }

    renderCart();
}

// 🔄 UBAH QTY
function ubahQty(id,delta){
    cart[id].qty += delta;

    if(cart[id].qty <= 0){
        delete cart[id];
    }

    renderCart();
}

// 🧾 RENDER CART
function renderCart(){
    const container = document.getElementById('selectedProducts');

    if(!Object.keys(cart).length){
        container.innerHTML = `
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
            Belum ada produk dipilih
        </div>`;
        hitungTotal();
        return;
    }

    container.innerHTML = '';

    Object.values(cart).forEach(item => {
        container.innerHTML += `
        <div class="product-card">
            <div class="product-card-image">
                ${getImage(item.produk?.foto)}
            </div>

            <div class="product-card-body">
                <div>
                    <div class="product-card-name">${item.produk?.nama_produk ?? '-'}</div>
                    <div style="font-size:12px;color:var(--text-gray);margin-bottom:2px;">
                        ${item.warna} / ${item.size}
                    </div>
                    <div class="product-card-price">
                        Rp${Number(item.harga).toLocaleString('id-ID')}
                    </div>
                </div>

                <div style="display:flex;justify-content:center;gap:12px;margin-top:12px;">
                    <button type="button" onclick="ubahQty(${item.id},-1)">-</button>
                    <span>${item.qty}</span>
                    <button type="button" onclick="ubahQty(${item.id},1)">+</button>
                </div>

                <input type="hidden" name="items[${item.id}][varian_id]" value="${item.id}">
                <input type="hidden" name="items[${item.id}][jumlah]" value="${item.qty}">
            </div>
        </div>`;
    });

    hitungTotal();
}

// 💰 HITUNG TOTAL
function hitungTotal(){
    let total = 0;

    Object.values(cart).forEach(item => {
        total += item.harga * item.qty;
    });

    document.getElementById('totalHarga').textContent =
        'Rp' + total.toLocaleString('id-ID');
}

// 💳 PILIH METODE
function pilihMetode(metode,btn){
    document.getElementById('metodePembayaran').value = metode;

    document.querySelectorAll('.payment-btn').forEach(b=>{
        b.classList.remove('active');
    });

    btn.classList.add('active');
}

// 🚀 SUBMIT
function submitKasir(){
    if(!Object.keys(cart).length){
        alert('Pilih produk dulu');
        return;
    }

    if(!document.getElementById('metodePembayaran').value){
        alert('Pilih metode pembayaran');
        return;
    }

    document.getElementById('kasirForm').submit();
}
</script>
@endpush