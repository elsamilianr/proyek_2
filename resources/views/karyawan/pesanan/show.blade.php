@extends('layouts.karyawan')

@section('title', 'Detail Pesanan')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('karyawan.pesanan.index') }}"
           style="display:flex;align-items:center;gap:4px;color:var(--text-gray);text-decoration:none;font-weight:600;font-size:14px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h1 class="page-title">Detail Pesanan</h1>
    </div>
</div>

{{-- Alert success/error --}}
@if(session('success'))
<div style="background:#DCFCE7;border:1px solid #BBF7D0;color:var(--green-dark);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-weight:600;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#FEE2E2;border:1px solid #FECACA;color:#DC2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-weight:600;">
    {{ session('error') }}
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- KOLOM KIRI --}}
    <div>

        {{-- Info Pesanan --}}
        <div class="table-card mb-24">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <div style="font-size:20px;font-weight:800;">{{ $pesanan->kode_pesanan }}</div>
                    <div style="font-size:13px;color:var(--text-gray);margin-top:2px;">
                        {{ $pesanan->tanggal?->translatedFormat('d F Y') ?? '-' }}
                    </div>
                </div>
                @php
                    $statusColor = [
                        'menunggu_pembayaran' => ['bg'=>'#FEF3C7','color'=>'#92400E'],
                        'menunggu_verifikasi' => ['bg'=>'#DBEAFE','color'=>'#1E40AF'],
                        'diproses'            => ['bg'=>'#EDE9FE','color'=>'#5B21B6'],
                        'selesai'             => ['bg'=>'#DCFCE7','color'=>'#15803D'],
                        'dibatalkan'          => ['bg'=>'#FEE2E2','color'=>'#B91C1C'],
                    ][$pesanan->status_pesanan] ?? ['bg'=>'#F3F4F6','color'=>'#6B7280'];
                @endphp
                <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};padding:6px 16px;border-radius:20px;font-weight:700;font-size:13px;">
                    {{ $pesanan->label_status }}
                </span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:16px;border-top:1px solid var(--pink-card);">
                <div>
                    <div class="pesanan-info-label">Pembeli</div>
                    <div class="pesanan-info-value">{{ $pesanan->pembeli->nama ?? '-' }}</div>
                    <div style="font-size:12px;color:var(--text-gray);">{{ $pesanan->pembeli->email ?? '' }}</div>
                </div>
                <div>
                    <div class="pesanan-info-label">No. HP</div>
                    <div class="pesanan-info-value">{{ $pesanan->pembeli->no_hp ?? '-' }}</div>
                </div>
                @if($pesanan->catatan)
                <div style="grid-column:1/-1;">
                    <div class="pesanan-info-label">Catatan</div>
                    <div class="pesanan-info-value">{{ $pesanan->catatan }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Detail Produk --}}
        <div class="table-card mb-24">
            <div style="font-size:16px;font-weight:800;margin-bottom:14px;">Produk Dipesan</div>
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
                        <td>
                            <div style="font-weight:700;">{{ $detail->varian->produk->nama_produk ?? '-' }}</div>
                        </td>
                        <td style="font-size:12px;color:var(--text-gray);">{{ $detail->varian->label ?: 'Default' }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $detail->jumlah }}</td>
                        <td style="text-align:right;">Rp{{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:700;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Ringkasan harga --}}
            <div style="border-top:1px solid var(--pink-card);margin-top:12px;padding-top:12px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:14px;">
                    <span style="color:var(--text-gray);">Subtotal</span>
                    <span>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($pesanan->diskon > 0)
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:14px;">
                    <span style="color:var(--text-gray);">
                        Diskon
                        @if($pesanan->promo) ({{ $pesanan->promo->nama_promo }}) @endif
                    </span>
                    <span style="color:#EF4444;">-Rp{{ number_format($pesanan->diskon, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;border-top:1px solid var(--pink-card);padding-top:10px;margin-top:6px;">
                    <span>Total</span>
                    <span>Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Ubah Status (hanya jika sudah diproses) --}}
        @if(in_array($pesanan->status_pesanan, ['diproses', 'selesai']) && $pesanan->status_pesanan !== 'dibatalkan')
        <div class="table-card mb-24">
            <div style="font-size:16px;font-weight:800;margin-bottom:14px;">Ubah Status Pesanan</div>
            <form method="POST" action="{{ route('karyawan.pesanan.update-status', $pesanan) }}">
                @csrf @method('PATCH')
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    @if($pesanan->status_pesanan === 'diproses')
                    <button name="status" value="selesai" type="submit"
                        style="background:var(--green-btn);color:white;border:none;border-radius:10px;padding:10px 20px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;">
                        ✓ Tandai Selesai
                    </button>
                    @endif
                    @if($pesanan->status_pesanan !== 'selesai')
                    <button name="status" value="dibatalkan" type="submit"
                        onclick="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')"
                        style="background:#FEE2E2;color:#DC2626;border:none;border-radius:10px;padding:10px 20px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;">
                        ✕ Batalkan Pesanan
                    </button>
                    @endif
                </div>
            </form>
        </div>
        @endif

    </div>

    {{-- KOLOM KANAN: Pembayaran --}}
    <div>
        <div class="table-card">
            <div style="font-size:16px;font-weight:800;margin-bottom:14px;">Info Pembayaran</div>

            @if($pesanan->pembayaran)
            @php $bayar = $pesanan->pembayaran; @endphp

            <div style="margin-bottom:12px;">
                <div class="pesanan-info-label">Metode</div>
                <div class="pesanan-info-value" style="text-transform:uppercase;">{{ $bayar->metode }}</div>
            </div>

            <div style="margin-bottom:12px;">
                <div class="pesanan-info-label">Status Pembayaran</div>
                @php
                    $sc = ['pending'=>['#FEF3C7','#92400E'],'menunggu_verifikasi'=>['#DBEAFE','#1E40AF'],'terverifikasi'=>['#DCFCE7','#15803D'],'ditolak'=>['#FEE2E2','#B91C1C']][$bayar->status_pembayaran] ?? ['#F3F4F6','#6B7280'];
                @endphp
                <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    {{ $bayar->label_status }}
                </span>
            </div>

            <div style="margin-bottom:12px;">
                <div class="pesanan-info-label">Jumlah Dibayar</div>
                <div class="pesanan-info-value">
                    Rp{{ number_format($bayar->jumlah_bayar ?? $pesanan->total_harga, 0, ',', '.') }}
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            @if($bayar->bukti_pembayaran)
            <div style="margin-bottom:16px;">
                <div class="pesanan-info-label">Bukti Transfer</div>
                <a href="{{ asset('storage/' . $bayar->bukti_pembayaran) }}" target="_blank"
                   style="display:block;margin-top:8px;border-radius:10px;overflow:hidden;border:2px solid var(--pink-card);">
                    <img src="{{ asset('storage/' . $bayar->bukti_pembayaran) }}"
                         alt="Bukti Pembayaran"
                         style="width:100%;max-height:220px;object-fit:cover;display:block;">
                </a>
                <a href="{{ asset('storage/' . $bayar->bukti_pembayaran) }}" target="_blank"
                   style="font-size:12px;color:var(--pink-hover);font-weight:600;margin-top:4px;display:inline-block;">
                    Lihat ukuran penuh ↗
                </a>
            </div>
            @else
            <div style="background:var(--pink-light);border-radius:10px;padding:16px;text-align:center;color:var(--text-gray);font-size:13px;margin-bottom:16px;">
                Bukti pembayaran belum diunggah
            </div>
            @endif

            {{-- Tombol Verifikasi --}}
            @if(in_array($bayar->status_pembayaran, ['pending', 'menunggu_verifikasi']) && $bayar->bukti_pembayaran)
            <form method="POST" action="{{ route('karyawan.pesanan.verifikasi-pembayaran', $pesanan) }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:10px;">
                    <label style="font-size:12px;font-weight:700;color:var(--text-gray);display:block;margin-bottom:4px;">
                        Catatan (opsional)
                    </label>
                    <textarea name="catatan" rows="2"
                        style="width:100%;border:1px solid var(--pink-border);border-radius:8px;padding:8px;font-family:'Nunito',sans-serif;font-size:13px;resize:none;outline:none;"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <button name="aksi" value="terima" type="submit"
                        style="background:var(--green-btn);color:white;border:none;border-radius:10px;padding:10px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;font-size:13px;">
                        ✓ Terima
                    </button>
                    <button name="aksi" value="tolak" type="submit"
                        onclick="return confirm('Tolak pembayaran ini?')"
                        style="background:#FEE2E2;color:#DC2626;border:none;border-radius:10px;padding:10px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;font-size:13px;">
                        ✕ Tolak
                    </button>
                </div>
            </form>
            @endif

            @else
            {{-- Belum ada data pembayaran --}}
            <div style="background:var(--pink-light);border-radius:10px;padding:24px;text-align:center;color:var(--text-gray);font-size:13px;">
                Pembeli belum melakukan pembayaran
            </div>
            @endif
        </div>
    </div>

</div>

@endsection