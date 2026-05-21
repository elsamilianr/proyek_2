@extends('layouts.karyawan')

@section('title', 'Promo')

@section('content')

<div class="page-header">
    <h1 class="page-title">Promo</h1>

    <div style="display:flex; gap:12px; align-items:center;">
        <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input type="text" placeholder="cari promo..." id="searchPromo" oninput="filterPromo(this.value)">
        </div>
        <a href="{{ route('karyawan.promo.create') }}" class="btn-tambah">+ Tambah</a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border-radius:12px;padding:12px 18px;margin-bottom:20px;color:#166534;font-weight:600;font-size:14px;">
    ✓ {{ session('success') }}
</div>
@endif

<div class="products-grid" id="promoGrid">

    @forelse($promos as $promo)
    @php
        $isAktif   = $promo->status === 'aktif';
        $isPending = $promo->status === 'pending';
        $produkList = $promo->produks->pluck('nama_produk')->implode(', ');
    @endphp

    <div class="product-card" data-name="{{ strtolower($promo->nama_promo) }}"
         style="display:flex;flex-direction:column;overflow:visible;">

        {{-- Banner diskon --}}
        <div style="
            background: linear-gradient(135deg, #F472B6 0%, #EC4899 100%);
            border-radius: 14px 14px 0 0;
            padding: 28px 20px 20px;
            position: relative;
            overflow: hidden;
        ">
            {{-- Lingkaran dekoratif --}}
            <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-30px;right:30px;width:70px;height:70px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>

            {{-- Badge status --}}
            <span style="
                position:absolute;top:12px;right:14px;
                padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;
                background:{{ $isAktif ? 'rgba(220,252,231,0.9)' : ($isPending ? 'rgba(254,243,199,0.9)' : 'rgba(254,226,226,0.9)') }};
                color:{{ $isAktif ? '#166534' : ($isPending ? '#92400e' : '#991b1b') }};
            ">{{ ucfirst($promo->status) }}</span>

            {{-- Nilai diskon besar --}}
            <div style="color:rgba(255,255,255,0.85);font-size:12px;font-weight:700;margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Diskon</div>
            <div style="color:#fff;font-size:36px;font-weight:900;line-height:1;">
                @if($promo->tipe_diskon === 'persen')
                    {{ rtrim(rtrim(number_format($promo->diskon, 2, '.', ''), '0'), '.') }}%
                @else
                    Rp{{ number_format($promo->diskon, 0, ',', '.') }}
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div style="padding:16px;flex:1;display:flex;flex-direction:column;gap:10px;">

            {{-- Nama promo --}}
            <div style="font-weight:800;font-size:15px;color:var(--text-dark);">
                {{ $promo->nama_promo }}
            </div>

            {{-- Deskripsi --}}
            @if($promo->deskripsi)
            <div style="font-size:12px;color:var(--text-gray);line-height:1.5;">
                {{ Str::limit($promo->deskripsi, 70) }}
            </div>
            @endif

            {{-- Info rows --}}
            <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;color:#555;">

                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:14px;">📅</span>
                    <span>{{ $promo->tanggal_mulai->format('d M Y') }} – {{ $promo->tanggal_selesai->format('d M Y') }}</span>
                </div>

                @if($promo->min_pembelian > 0)
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:14px;">🛒</span>
                    <span>Min. Rp{{ number_format($promo->min_pembelian, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($produkList)
                <div style="display:flex;align-items:flex-start;gap:8px;">
                    <span style="font-size:14px;">🏷️</span>
                    <span style="line-height:1.4;">{{ Str::limit($produkList, 60) }}</span>
                </div>
                @else
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:14px;">🏷️</span>
                    <span style="color:#bbb;">Semua produk</span>
                </div>
                @endif

            </div>
        </div>

        {{-- Aksi --}}
        <div style="display:flex;gap:8px;padding:0 16px 16px;">
            <a href="{{ route('karyawan.promo.show', $promo->id) }}"
               style="flex:1;padding:8px;background:var(--pink-light);border-radius:10px;text-align:center;font-weight:700;font-size:13px;text-decoration:none;color:var(--text-dark);">
                Detail
            </a>
            <form action="{{ route('karyawan.promo.destroy', $promo->id) }}"
                  method="POST" onsubmit="return confirm('Hapus promo ini?')" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit"
                    style="width:100%;padding:8px;background:#fee2e2;border:none;border-radius:10px;font-family:'Nunito',sans-serif;font-weight:700;font-size:13px;cursor:pointer;color:#dc2626;">
                    Hapus
                </button>
            </form>
        </div>

    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
        <div style="font-size:48px;margin-bottom:16px;">🎁</div>
        <p style="font-size:16px;font-weight:600;margin-bottom:8px;">Belum ada promo</p>
        <p style="font-size:13px;">Klik <strong>+ Tambah</strong> untuk membuat promo pertama.</p>
    </div>
    @endforelse

</div>

@if($promos->hasPages())
<div style="margin-top:24px;">{{ $promos->links() }}</div>
@endif

@endsection

@push('scripts')
<script>
function filterPromo(val) {
    document.querySelectorAll('#promoGrid .product-card').forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = name.includes(val.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush