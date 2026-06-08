@extends('layouts.karyawan')

@section('title', 'Detail Pesanan Online')

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

.badge-online {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 22px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 800;
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

.catatan-box {
    background: #fdf2f8;
    border: 1px solid #fce7f3;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 14px;
    color: #374151;
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
        <h1 class="page-title" style="margin:0;">Detail Pesanan Online</h1>
    </div>
</div>

@php
    $statusColor = [
        'menunggu_pembayaran' => ['bg' => '#FEF3C7', 'color' => '#92400E'],
        'menunggu_verifikasi' => ['bg' => '#DBEAFE', 'color' => '#1E40AF'],
        'diproses'            => ['bg' => '#EDE9FE', 'color' => '#5B21B6'],
        'selesai'             => ['bg' => '#DCFCE7', 'color' => '#15803D'],
        'dibatalkan'          => ['bg' => '#FEE2E2', 'color' => '#B91C1C'],
    ][$pesanan->status_pesanan] ?? ['bg' => '#F3F4F6', 'color' => '#6B7280'];

    $labelMetodePembayaran = match($pesanan->pembayaran?->metode ?? '') {
        'qris'        => 'QRIS',
        'gopay'       => 'GoPay',
        'credit_card' => 'Kartu Kredit',
        'transfer'    => 'Transfer Bank',
        'cash'        => 'Cash',
        'midtrans'    => 'Midtrans',
        default       => strtoupper($pesanan->pembayaran?->metode ?? '-'),
    };
@endphp

<div class="detail-wrapper">

    {{-- ===== INFO PESANAN ===== --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;">
            <div>
                <div class="trx-code">{{ $pesanan->kode_pesanan }}</div>
                <div class="trx-date">
                    {{ $pesanan->tanggal?->translatedFormat('d F Y') ?? ($pesanan->created_at?->translatedFormat('d F Y') ?? '-') }}
                </div>
            </div>
            <span class="badge-online"
                  style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};">
                {{ $pesanan->label_status }}
            </span>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">Pembeli</div>
                <div class="info-value">{{ $pesanan->pembeli?->nama ?? '-' }}</div>
                <small style="color:#64748b;">{{ $pesanan->pembeli?->email ?? '' }}</small>
            </div>
            <div>
                <div class="info-label">No HP</div>
                <div class="info-value">{{ $pesanan->pembeli?->no_hp ?? '-' }}</div>
            </div>
            @if($pesanan->pembayaran)
            <div>
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">
                    <span class="badge-method">{{ $labelMetodePembayaran }}</span>
                </div>
            </div>
            @endif
            @if($pesanan->promo)
            <div>
                <div class="info-label">Promo Dipakai</div>
                <div class="info-value" style="color:var(--pink-btn, #e84393);">
                    {{ $pesanan->promo->kode }} — {{ $pesanan->promo->nama_promo }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== PRODUK + RINGKASAN ===== --}}
    <div class="two-column">

        {{-- Daftar produk --}}
        <div class="detail-card">
            <div class="section-title">Produk Dipesan</div>

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
                    @foreach($pesanan->details as $detail)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($detail->varian?->produk?->foto)
                                    <img src="{{ asset('storage/' . $detail->varian->produk->foto) }}"
                                         alt="{{ $detail->varian->produk->nama_produk }}"
                                         class="product-thumb">
                                @else
                                    <div class="product-thumb">📦</div>
                                @endif
                                <span style="font-weight:700;">
                                    {{ $detail->varian?->produk?->nama_produk ?? 'Produk' }}
                                </span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $detail->varian?->label ?: 'Default' }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $detail->jumlah }}</td>
                        <td style="text-align:right;">
                            Rp{{ number_format($detail->harga, 0, ',', '.') }}
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
                    <span>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($pesanan->diskon > 0)
                <div class="summary-row">
                    <span style="color:#64748b;">
                        Diskon
                        @if($pesanan->promo)
                            <small style="font-weight:600;">({{ $pesanan->promo->kode }})</small>
                        @endif
                    </span>
                    <span style="color:#ef4444;font-weight:700;">
                        -Rp{{ number_format($pesanan->diskon, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span style="color:var(--pink-btn, #e84393);">
                        Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: Info pembayaran + catatan --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Info pembayaran --}}
            <div class="detail-card">
                <div class="section-title">Info Pembayaran</div>

                @if($pesanan->pembayaran)
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div>
                            <div class="info-label">Metode</div>
                            <div class="info-value">{{ $labelMetodePembayaran }}</div>
                        </div>
                        <div>
                            <div class="info-label">Status</div>
                            <div class="info-value" style="font-size:14px;">
                                {{ $pesanan->pembayaran->label_status }}
                            </div>
                        </div>
                        <div>
                            <div class="info-label">Total Tagihan</div>
                            <div class="info-value" style="color:var(--pink-btn, #e84393);">
                                Rp{{ number_format($pesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Bukti Pembayaran --}}
                        @if($pesanan->pembayaran->bukti_pembayaran)
                        <div>
                            <div class="info-label">Bukti Pembayaran</div>
                            <a href="{{ Storage::url($pesanan->pembayaran->bukti_pembayaran) }}"
                               target="_blank" title="Lihat bukti pembayaran">
                                <img src="{{ Storage::url($pesanan->pembayaran->bukti_pembayaran) }}"
                                     alt="Bukti Pembayaran"
                                     style="width:100%;border-radius:12px;border:1px solid #fce7f3;
                                            margin-top:6px;object-fit:cover;cursor:zoom-in;">
                            </a>
                            <p style="font-size:11px;color:#94a3b8;margin-top:6px;">
                                Klik gambar untuk memperbesar
                            </p>
                        </div>
                        @endif
                    </div>
                @else
                    <div style="background:#fdf2f8;border-radius:14px;padding:24px;text-align:center;color:#64748b;font-size:14px;">
                        Belum ada data pembayaran
                    </div>
                @endif
            </div>

            {{-- Catatan pembeli --}}
            @if($pesanan->catatan)
            <div class="detail-card">
                <div class="section-title">Catatan Pembeli</div>
                <div class="catatan-box">
                    {{ $pesanan->catatan }}
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

@endsection