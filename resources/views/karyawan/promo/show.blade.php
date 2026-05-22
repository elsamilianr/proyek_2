@extends('layouts.karyawan')

@section('title', 'Detail Promo')

@section('content')

@php
    $isAktif   = $promo->status === 'aktif';
    $isPending = $promo->status === 'pending';
    $statusColor = $isAktif ? '#166534' : ($isPending ? '#92400e' : '#991b1b');
    $statusBg    = $isAktif ? '#dcfce7'  : ($isPending ? '#fef3c7'  : '#fee2e2');
    $diskonLabel = $promo->tipe_diskon === 'persen'
        ? rtrim(rtrim(number_format($promo->diskon, 2, '.', ''), '0'), '.') . '%'
        : 'Rp' . number_format($promo->diskon, 0, ',', '.');
@endphp

{{-- Back --}}
<div style="margin-bottom:24px;">
    <a href="{{ route('karyawan.promo.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-weight:700;font-size:14px;color:var(--text-gray);text-decoration:none;">
        ← Kembali ke Daftar Promo
    </a>
</div>

<div style="max-width:720px;">

    {{-- Header card --}}
    <div style="
        background: linear-gradient(135deg, #F472B6 0%, #EC4899 100%);
        border-radius: 20px;
        padding: 36px 32px;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    ">
        {{-- Dekorasi lingkaran --}}
        <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-40px;right:80px;width:100px;height:100px;background:rgba(255,255,255,0.07);border-radius:50%;"></div>
        <div style="position:absolute;top:40px;right:60px;width:50px;height:50px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>

        {{-- Status badge --}}
        <span style="
            display:inline-block;
            padding:4px 14px;border-radius:999px;font-size:12px;font-weight:700;
            background:{{ $statusBg }};color:{{ $statusColor }};
            margin-bottom:16px;
        ">{{ ucfirst($promo->status) }}</span>

        {{-- Nama promo --}}
        <div style="color:rgba(255,255,255,0.85);font-size:13px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">
            Nama Promo
        </div>
        <div style="color:#fff;font-size:26px;font-weight:900;margin-bottom:20px;">
            {{ $promo->nama_promo }}
        </div>

        {{-- Nilai diskon --}}
        <div style="display:inline-block;background:rgba(255,255,255,0.2);border-radius:14px;padding:14px 24px;">
            <div style="color:rgba(255,255,255,0.8);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;">
                {{ $promo->tipe_diskon === 'persen' ? 'Diskon Persen' : 'Diskon Nominal' }}
            </div>
            <div style="color:#fff;font-size:40px;font-weight:900;line-height:1;">
                {{ $diskonLabel }}
            </div>
        </div>
    </div>

    {{-- Info cards --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

        {{-- Tanggal --}}
        <div style="background:#fff;border-radius:16px;border:2px solid var(--pink-card);padding:20px;">
            <div style="font-size:12px;color:var(--text-gray);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">📅 Periode Promo</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div>
                    <div style="font-size:11px;color:#aaa;margin-bottom:2px;">Mulai</div>
                    <div style="font-weight:700;font-size:15px;color:var(--text-dark);">{{ $promo->tanggal_mulai->format('d M Y') }}</div>
                </div>
                <div style="border-top:1px dashed #eee;padding-top:8px;">
                    <div style="font-size:11px;color:#aaa;margin-bottom:2px;">Selesai</div>
                    <div style="font-weight:700;font-size:15px;color:var(--text-dark);">{{ $promo->tanggal_selesai->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Min pembelian --}}
        <div style="background:#fff;border-radius:16px;border:2px solid var(--pink-card);padding:20px;">
            <div style="font-size:12px;color:var(--text-gray);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">🛒 Syarat</div>
            <div>
                <div style="font-size:11px;color:#aaa;margin-bottom:2px;">Minimal Pembelian</div>
                <div style="font-weight:700;font-size:15px;color:var(--text-dark);">
                    @if($promo->min_pembelian > 0)
                        Rp{{ number_format($promo->min_pembelian, 0, ',', '.') }}
                    @else
                        <span style="color:#bbb;">Tidak ada</span>
                    @endif
                </div>
            </div>
            <div style="border-top:1px dashed #eee;padding-top:8px;margin-top:8px;">
                <div style="font-size:11px;color:#aaa;margin-bottom:2px;">Tipe Diskon</div>
                <div style="font-weight:700;font-size:15px;color:var(--text-dark);">{{ ucfirst($promo->tipe_diskon) }}</div>
            </div>
        </div>

    </div>

    {{-- Deskripsi --}}
    @if($promo->deskripsi)
    <div style="background:#fff;border-radius:16px;border:2px solid var(--pink-card);padding:20px;margin-bottom:20px;">
        <div style="font-size:12px;color:var(--text-gray);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">📝 Deskripsi</div>
        <p style="font-size:14px;color:var(--text-dark);line-height:1.7;margin:0;">{{ $promo->deskripsi }}</p>
    </div>
    @endif

    {{-- Produk yang berlaku --}}
    <div style="background:#fff;border-radius:16px;border:2px solid var(--pink-card);padding:20px;margin-bottom:20px;">
        <div style="font-size:12px;color:var(--text-gray);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">🏷️ Berlaku untuk Produk</div>
        @if($promo->produks->isEmpty())
            <div style="font-size:14px;color:#bbb;font-style:italic;">Semua produk aktif</div>
        @else
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($promo->produks as $produk)
                <span style="
                    background:var(--pink-light);
                    border:1px solid var(--pink-card);
                    border-radius:999px;
                    padding:5px 14px;
                    font-size:13px;
                    font-weight:700;
                    color:var(--text-dark);
                ">{{ $produk->nama_produk }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Pengaju --}}
    <div style="background:#fff;border-radius:16px;border:2px solid var(--pink-card);padding:20px;margin-bottom:28px;">
        <div style="font-size:12px;color:var(--text-gray);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">👤 Informasi Pengajuan</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="font-size:11px;color:#aaa;margin-bottom:3px;">Diajukan oleh</div>
                <div style="font-weight:700;font-size:14px;color:var(--text-dark);">{{ $promo->pengaju->nama ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:#aaa;margin-bottom:3px;">Disetujui oleh</div>
                <div style="font-weight:700;font-size:14px;color:var(--text-dark);">{{ $promo->penyetuju->nama ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:#aaa;margin-bottom:3px;">Tanggal dibuat</div>
                <div style="font-weight:700;font-size:14px;color:var(--text-dark);">{{ $promo->created_at->format('d M Y, H:i') }}</div>
            </div>
            @if($promo->updated_at != $promo->created_at)
            <div>
                <div style="font-size:11px;color:#aaa;margin-bottom:3px;">Terakhir diubah</div>
                <div style="font-weight:700;font-size:14px;color:var(--text-dark);">{{ $promo->updated_at->format('d M Y, H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Tombol hapus --}}
    @if($promo->status !== 'aktif' && $promo->diajukan_oleh == auth()->id())
    <form action="{{ route('karyawan.promo.destroy', $promo->id) }}"
          method="POST" onsubmit="return confirm('Yakin ingin menghapus promo ini?')">
        @csrf @method('DELETE')
        <button type="submit" style="
            width:100%;padding:12px;
            background:#fee2e2;border:2px solid #fca5a5;border-radius:12px;
            font-family:'Nunito',sans-serif;font-weight:700;font-size:14px;
            cursor:pointer;color:#dc2626;
        ">Hapus Promo</button>
    </form>
    @endif

</div>

@endsection