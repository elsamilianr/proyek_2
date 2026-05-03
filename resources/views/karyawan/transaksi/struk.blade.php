@extends('layouts.karyawan')

@section('title', 'Struk Transaksi')

@push('styles')
<style>
    @media print {
        .navbar, .sidebar, .page-header, .no-print { display: none !important; }
        .main-content { display: block !important; }
        .page-content { padding: 0 !important; background: white !important; }
        .struk-wrapper { max-width: 100% !important; box-shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('content')

<div class="page-header no-print">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('karyawan.transaksi.create') }}"
           style="display:flex;align-items:center;gap:4px;color:var(--text-gray);text-decoration:none;font-weight:600;font-size:14px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Transaksi Baru
        </a>
        <h1 class="page-title">Struk Transaksi</h1>
    </div>
    <button onclick="window.print()"
        style="background:var(--pink-btn);color:white;border:none;border-radius:12px;padding:10px 24px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak Struk
    </button>
</div>

{{-- STRUK --}}
<div style="max-width:420px;margin:0 auto;">
    <div class="struk-wrapper table-card"
         style="padding:28px 28px 24px;font-family:'Nunito',sans-serif;border:1px dashed var(--pink-border);">

        {{-- Header toko --}}
        <div style="text-align:center;margin-bottom:20px;padding-bottom:16px;border-bottom:1px dashed var(--pink-border);">
            <div style="font-size:24px;font-weight:800;color:var(--text-dark);">Hayki</div>
            <div style="font-size:13px;color:var(--text-gray);margin-top:2px;">Hijab & Fashion Muslim</div>
            <div style="font-size:12px;color:var(--text-gray);margin-top:2px;">Kasir: {{ $transaksi->karyawan->nama ?? 'Karyawan' }}</div>
        </div>

        {{-- Info transaksi --}}
        <div style="margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed var(--pink-border);">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px;">
                <span style="color:var(--text-gray);">Kode Transaksi</span>
                <span style="font-weight:700;">{{ $transaksi->kode_transaksi }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px;">
                <span style="color:var(--text-gray);">Tanggal</span>
                <span>{{ $transaksi->created_at->translatedFormat('d F Y, H:i') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--text-gray);">Metode Bayar</span>
                <span style="text-transform:uppercase;font-weight:700;">{{ $transaksi->metode_bayar }}</span>
            </div>
        </div>

        {{-- Item produk --}}
        <div style="margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed var(--pink-border);">
            @foreach($transaksi->details as $detail)
            <div style="margin-bottom:10px;">
                <div style="font-weight:700;font-size:14px;">
                    {{ $detail->varian->produk->nama_produk ?? 'Produk' }}
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2px;">
                    <span style="font-size:12px;color:var(--text-gray);">
                        {{ $detail->varian->label ?: 'Default' }}
                        &nbsp;×&nbsp;{{ $detail->jumlah }}
                        &nbsp;@&nbsp;Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}
                    </span>
                    <span style="font-weight:700;font-size:14px;">
                        Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Ringkasan harga --}}
        <div style="margin-bottom:16px;padding-bottom:14px;border-bottom:2px solid var(--text-dark);">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                <span style="color:var(--text-gray);">Subtotal</span>
                <span>Rp{{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaksi->diskon > 0)
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                <span style="color:var(--text-gray);">
                    Diskon{{ $transaksi->promo ? ' ('.$transaksi->promo->nama_promo.')' : '' }}
                </span>
                <span style="color:#EF4444;">-Rp{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:800;margin-top:8px;">
                <span>TOTAL</span>
                <span>Rp{{ number_format($transaksi->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Uang & kembalian --}}
        @if($transaksi->metode_bayar === 'cash' && $transaksi->uang_diterima)
        <div style="margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed var(--pink-border);">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                <span style="color:var(--text-gray);">Uang Diterima</span>
                <span>Rp{{ number_format($transaksi->uang_diterima, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
                <span>Kembalian</span>
                <span>Rp{{ number_format(max(0, $transaksi->kembalian ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div style="text-align:center;margin-top:8px;">
            <div style="font-size:13px;color:var(--text-gray);margin-bottom:4px;">Terima kasih sudah berbelanja!</div>
            <div style="font-size:12px;color:var(--pink-primary);font-weight:700;">✿ Hayki ✿</div>
        </div>
    </div>

    {{-- Tombol aksi bawah --}}
    <div class="no-print" style="display:flex;gap:10px;margin-top:16px;justify-content:center;">
        <a href="{{ route('karyawan.transaksi.create') }}"
           style="background:var(--green-btn);color:white;border-radius:12px;padding:10px 24px;text-decoration:none;font-weight:700;font-size:14px;">
            + Transaksi Baru
        </a>
        <a href="{{ route('karyawan.riwayat.index') }}"
           style="background:var(--pink-light);color:var(--text-dark);border-radius:12px;padding:10px 24px;text-decoration:none;font-weight:700;font-size:14px;border:1px solid var(--pink-border);">
            Lihat Riwayat
        </a>
    </div>
</div>

@endsection