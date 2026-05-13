@extends('layouts.karyawan')

@section('title', 'Detail Pesanan')

@section('content')

<style>
.detail-wrapper{
    display:flex;
    flex-direction:column;
    gap:24px;
}

.two-column{
    display:grid;
    grid-template-columns:1fr 340px;
    gap:24px;
    align-items:start;
}

.detail-card{
    background:#fff;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
    border:1px solid #f4d9e3;
}

.section-title{
    font-size:18px;
    font-weight:800;
    margin-bottom:18px;
    color:#1e293b;
}

.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    padding-top:18px;
    border-top:1px solid #fce7f3;
}

.info-label{
    font-size:13px;
    color:#64748b;
    font-weight:600;
    margin-bottom:4px;
}

.info-value{
    font-size:17px;
    font-weight:700;
    color:#0f172a;
}

.order-code{
    font-size:24px;
    font-weight:900;
    color:#0f172a;
}

.order-date{
    font-size:13px;
    color:#64748b;
    margin-top:4px;
}

.status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:170px;
    min-height:46px;
    padding:10px 22px;
    border-radius:999px;
    font-size:14px;
    font-weight:800;
    white-space:nowrap;
}

.payment-empty{
    background:#fdf2f8;
    border-radius:14px;
    padding:28px;
    text-align:center;
    color:#64748b;
    font-size:14px;
}

.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table th{
    text-align:left;
    padding:14px 12px;
    font-size:13px;
    font-weight:700;
    border-bottom:2px solid #fce7f3;
}

.data-table td{
    padding:18px 12px;
    border-bottom:1px solid #fce7f3;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
}

.summary-total{
    font-size:20px;
    font-weight:800;
    border-top:2px solid #fce7f3;
    padding-top:14px;
    margin-top:8px;
}

@media(max-width:1024px){
    .two-column{
        grid-template-columns:1fr;
    }

    .info-grid{
        grid-template-columns:1fr;
    }
}
</style>

<div class="page-header" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:14px;">
        <a href="{{ route('karyawan.pesanan.index') }}"
           style="display:flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-weight:700;">
            ← Kembali
        </a>
        <h1 class="page-title" style="margin:0;">Detail Pesanan</h1>
    </div>
</div>

<div class="detail-wrapper">

    {{-- INFO PESANAN FULL --}}
    <div class="detail-card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;">
            <div>
                <div class="order-code">{{ $pesanan->kode_pesanan }}</div>
                <div class="order-date">
                    {{ $pesanan->tanggal?->translatedFormat('d F Y') ?? '-' }}
                </div>
            </div>

            @php
                $statusColor = [
                    'menunggu_pembayaran' => ['bg'=>'#FEF3C7','color'=>'#92400E'],
                    'menunggu_verifikasi' => ['bg'=>'#DBEAFE','color'=>'#1E40AF'],
                    'diproses' => ['bg'=>'#EDE9FE','color'=>'#5B21B6'],
                    'selesai' => ['bg'=>'#DCFCE7','color'=>'#15803D'],
                    'dibatalkan' => ['bg'=>'#FEE2E2','color'=>'#B91C1C'],
                ][$pesanan->status_pesanan] ?? ['bg'=>'#F3F4F6','color'=>'#6B7280'];
            @endphp

            <span class="status-badge"
                style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }}">
                {{ $pesanan->label_status }}
            </span>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">Pembeli</div>
                <div class="info-value">{{ $pesanan->pembeli->nama ?? '-' }}</div>
                <small>{{ $pesanan->pembeli->email ?? '' }}</small>
            </div>

            <div>
                <div class="info-label">No HP</div>
                <div class="info-value">{{ $pesanan->pembeli->no_hp ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- PRODUK + PEMBAYARAN --}}
    <div class="two-column">

        {{-- PRODUK --}}
        <div class="detail-card">
            <div class="section-title">Produk Dipesan</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Harga</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->details as $detail)
                    <tr>
                        <td><strong>{{ $detail->varian->produk->nama_produk }}</strong></td>
                        <td>{{ $detail->varian->label }}</td>
                        <td style="text-align:center;">{{ $detail->jumlah }}</td>
                        <td style="text-align:right;">Rp{{ number_format($detail->harga,0,',','.') }}</td>
                        <td style="text-align:right;font-weight:700;">
                            Rp{{ number_format($detail->subtotal,0,',','.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:18px;">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rp{{ number_format($pesanan->subtotal,0,',','.') }}</span>
                </div>

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>Rp{{ number_format($pesanan->total_harga,0,',','.') }}</span>
                </div>
            </div>
        </div>

        {{-- PEMBAYARAN --}}
        <div class="detail-card">
            <div class="section-title">Info Pembayaran</div>

            @if($pesanan->pembayaran)
                <div class="info-grid" style="grid-template-columns:1fr;">
                    <div>
                        <div class="info-label">Metode</div>
                        <div class="info-value">{{ strtoupper($pesanan->pembayaran->metode) }}</div>
                    </div>

                    <div>
                        <div class="info-label">Jumlah</div>
                        <div class="info-value">
                            Rp{{ number_format($pesanan->pembayaran->jumlah_bayar,0,',','.') }}
                        </div>
                    </div>
                </div>
            @else
                <div class="payment-empty">
                    Pembeli belum melakukan pembayaran
                </div>
            @endif
        </div>
    </div>

    {{-- UBAH STATUS FULL --}}
    @if($pesanan->status_pesanan !== 'dibatalkan')
    <div class="detail-card">
        <div class="section-title">Ubah Status Pesanan</div>

        <form method="POST" action="{{ route('karyawan.pesanan.update-status', $pesanan) }}">
            @csrf
            @method('PATCH')

            <div style="display:flex;gap:12px;flex-wrap:wrap;">

                @if($pesanan->status_pesanan === 'diproses')
                <button name="status" value="selesai" type="submit"
                    style="background:#22C55E;color:white;border:none;border-radius:12px;padding:12px 22px;font-weight:700;cursor:pointer;">
                    ✓ Tandai Selesai
                </button>
                @endif

                @if($pesanan->status_pesanan !== 'selesai')
                <button name="status" value="dibatalkan" type="submit"
                    onclick="return confirm('Batalkan pesanan ini?')"
                    style="background:#FEE2E2;color:#DC2626;border:none;border-radius:12px;padding:12px 22px;font-weight:700;cursor:pointer;">
                    ✕ Batalkan Pesanan
                </button>
                @endif

            </div>
        </form>
    </div>
    @endif

</div>

@endsection