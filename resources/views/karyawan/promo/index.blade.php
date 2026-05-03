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

        <a href="{{ route('karyawan.promo.create') }}" class="btn-tambah">
            + Tambah
        </a>
    </div>
</div>

<div class="products-grid" id="promoGrid">

    @forelse($promos as $promo)
    <div class="product-card" data-name="{{ strtolower($promo->nama_promo) }}">

        <div class="product-card-image">
            @if($promo->foto)
                <img src="{{ asset('storage/' . $promo->foto) }}"
                     alt="{{ $promo->nama_promo }}"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <div style="width:100%;height:100%;background:#fdf2f8;display:flex;align-items:center;justify-content:center;color:#ec4899;font-size:42px;">
                    🎁
                </div>
            @endif
        </div>

        <div class="product-card-body">
            <div>
                <div class="product-card-name">
                    {{ $promo->nama_promo }}
                </div>

                <div style="font-size:13px;color:var(--text-gray);margin-bottom:6px;">
                    @if($promo->tipe_diskon === 'persen')
                        Diskon {{ $promo->diskon }}%
                    @else
                        Diskon Rp{{ number_format($promo->diskon,0,',','.') }}
                    @endif
                </div>

                <div style="font-size:12px;color:#888;">
                    {{ $promo->tanggal_mulai->format('d M Y') }}
                    -
                    {{ $promo->tanggal_selesai->format('d M Y') }}
                </div>

                <div style="margin-top:8px;">
                    <span style="
                        padding:5px 10px;
                        border-radius:999px;
                        font-size:11px;
                        font-weight:700;
                        background:
                        {{ $promo->status === 'aktif' ? '#dcfce7' : '#fef3c7' }};
                        color:
                        {{ $promo->status === 'aktif' ? '#166534' : '#92400e' }};
                    ">
                        {{ ucfirst($promo->status) }}
                    </span>
                </div>
            </div>

            <div class="product-card-menu" onclick="toggleMenu({{ $promo->id }})">
                •••
            </div>
        </div>

        <div id="menu-{{ $promo->id }}" style="display:none; padding:0 16px 12px;">

            <a href="{{ route('karyawan.promo.show', $promo->id) }}"
                style="display:block;padding:8px;background:var(--pink-light);border-radius:8px;text-align:center;font-weight:700;text-decoration:none;color:var(--text-dark);margin-bottom:6px;">
                Detail
            </a>

            <form action="{{ route('karyawan.promo.destroy', $promo->id) }}"
                method="POST"
                onsubmit="return confirm('Hapus promo ini?')">
                @csrf
                @method('DELETE')

                <button type="submit"
                    style="width:100%;padding:8px;background:#fee2e2;border:none;border-radius:8px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;color:#dc2626;">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#aaa;">
        <div style="font-size:48px;margin-bottom:16px;">🎁</div>
        <p style="font-size:16px;font-weight:600;margin-bottom:8px;">
            Belum ada promo
        </p>
        <p style="font-size:13px;">
            Klik <strong>+ Tambah</strong> untuk membuat promo pertama.
        </p>
    </div>
    @endforelse

</div>

@if($promos->hasPages())
<div style="margin-top:24px;">
    {{ $promos->links() }}
</div>
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

function toggleMenu(id) {
    document.querySelectorAll('[id^="menu-"]').forEach(menu => {
        if(menu.id !== 'menu-' + id){
            menu.style.display = 'none';
        }
    });

    const menu = document.getElementById('menu-' + id);

    if(menu){
        menu.style.display =
            menu.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endpush