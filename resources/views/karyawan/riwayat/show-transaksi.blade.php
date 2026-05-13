@extends('layouts.karyawan')

@section('title', 'Detail Transaksi')

@section('content')

<style>
.detail-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.two-column {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    align-items: start;
}

.detail-card {
    background: #fff;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    border: 1px solid #f4d9e3;
}

.section-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 18px;
    color: #1e293b;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    padding-top: 18px;
    border-top: 1px solid #fce7f3;
}

.info-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 4px;
}

.info-value {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
}

.trx-code {
    font-size: 24px;
    font-weight: 900;
    color: #0f172a;
}

.trx-date {
    font-size: 13px;
    color: #64748b;
    margin-top: 4px;
}

.badge-offline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 22px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 800;
    background: #fef3c7;
    color: #92400e;
    white-space: nowrap;
}

.badge-method {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    background: var(--pink-light, #fdf2f8);
    color: var(--text-dark, #0f172a);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    text-align: left;
    padding: 14px 12px;
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    border-bottom: 2px solid #fce7f3;
}

.data-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #fce7f3;
    font-size: 14px;
    vertical-align: middle;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

.product-thumb {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    background: var(--pink-light, #fdf2f8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    font-size: 14px;
}

.summary-total {
    font-size: 20px;
    font-weight: 800;
    border-top: 2px solid #fce7f3;
    padding-top: 14px;
    margin-top: 4px;
}

.cash-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cash-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
}

.catatan-box {
    background: #fffbeb;
    border: 1px dashed #fcd34d;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 14px;
    color: #78350f;
    line-height: 1.6;
}

@media (max-width: 1024px) {
    .two-column {
        grid-template-columns: 1fr;
    }
    .info-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 640px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    .trx-code {
        font-size: 18px;
    }
}
</style>

{{-- PAGE HEADER --}}
<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:14px;">
        <a href="{{ route('karyawan.riwayat.index') }}"
           style="display:flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-weight:700;font-size:14px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                 style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Riwayat
        </a>
        <h1 class="page-title" style="margin:0;">Detail Transaksi</h1>
    </div>

    {{-- Tombol cetak struk --}}
    <a href="{{ route('karyawan.transaksi.struk', $transaksi->id) }}"
       style="display:flex;align-items:center;gap:8px;background:var(--pink-btn, #e84393);color:white;
              border-radius:12px;padding:10px 20px;text-decoration:none;font-weight:700;font-size:14px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             style="width:18px;height:18px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Struk
    </a>
</div>

<div class="detail-wrapper">

    {{-- ===== INFO TRANSAKSI ===== --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;">
            <div>
                <div class="trx-code">{{ $transaksi->kode_transaksi }}</div>
                <div class="trx-date">
                    {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }} WIB
                </div>
            </div>
            <span class="badge-offline">Transaksi Offline</span>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">Kasir / Karyawan</div>
                <div class="info-value">{{ $transaksi->karyawan?->nama ?? '-' }}</div>
            </div>
            <div>
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">
                    <span class="badge-method">{{ strtoupper($transaksi->metode_bayar) }}</span>
                </div>
            </div>
            @if($transaksi->promo)
            <div>
                <div class="info-label">Promo Dipakai</div>
                <div class="info-value" style="color:var(--pink-btn, #e84393);">
                    {{ $transaksi->promo->nama_promo }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== PRODUK + RINGKASAN ===== --}}
    <div class="two-column">

        {{-- Daftar produk --}}
        <div class="detail-card">
            <div class="section-title">Produk Dibeli</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->details as $detail)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($detail->varian?->produk?->foto)
                                    <img src="{{ asset('storage/' . $detail->varian->produk->foto) }}"
                                         alt="{{ $detail->varian->produk->nama_produk }}"
                                         class="product-thumb">
                                @else
                                    <div class="product-thumb">🧥</div>
                                @endif
                                <span style="font-weight:700;">
                                    {{ $detail->varian?->produk?->nama_produk ?? 'Produk' }}
                                </span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $detail->varian?->label ?: 'Default' }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $detail->jumlah }}</td>
                        <td style="text-align:right;">
                            Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right;font-weight:700;">
                            Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Ringkasan harga --}}
            <div style="margin-top:16px;">
                <div class="summary-row">
                    <span style="color:#64748b;">Subtotal</span>
                    <span>Rp{{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($transaksi->diskon > 0)
                <div class="summary-row">
                    <span style="color:#64748b;">
                        Diskon
                        @if($transaksi->promo)
                            <small style="font-weight:600;">({{ $transaksi->promo->nama_promo }})</small>
                        @endif
                    </span>
                    <span style="color:#ef4444;font-weight:700;">
                        -Rp{{ number_format($transaksi->diskon, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span style="color:var(--pink-btn, #e84393);">
                        Rp{{ number_format($transaksi->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: Info pembayaran + catatan --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Info pembayaran --}}
            <div class="detail-card">
                <div class="section-title">Info Pembayaran</div>

                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <div class="info-label">Metode</div>
                        <div class="info-value">{{ strtoupper($transaksi->metode_bayar) }}</div>
                    </div>
                    <div>
                        <div class="info-label">Total Tagihan</div>
                        <div class="info-value" style="color:var(--pink-btn, #e84393);">
                            Rp{{ number_format($transaksi->total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($transaksi->metode_bayar === 'cash' && $transaksi->uang_diterima)
                <div class="cash-box" style="margin-top:18px;">
                    <div class="cash-row">
                        <span style="color:#15803d;font-weight:600;">Uang Diterima</span>
                        <span style="font-weight:700;">
                            Rp{{ number_format($transaksi->uang_diterima, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="cash-row" style="border-top:1px solid #bbf7d0;padding-top:10px;">
                        <span style="color:#15803d;font-weight:800;">Kembalian</span>
                        <span style="font-weight:800;font-size:16px;">
                            Rp{{ number_format(max(0, $transaksi->kembalian ?? 0), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Catatan (jika ada) --}}
            @if($transaksi->catatan)
            <div class="detail-card">
                <div class="section-title">Catatan</div>
                <div class="catatan-box">
                    {{ $transaksi->catatan }}
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

@endsection