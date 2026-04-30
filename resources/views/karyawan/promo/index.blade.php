@extends('layouts.karyawan')

@section('title', 'Promo')

@section('content')

<div class="page-header">
    <h1 class="page-title">Promo</h1>
    <div style="display:flex;gap:12px;align-items:center;">
        <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input type="text" placeholder="cari..." oninput="filterPromo(this.value)">
        </div>
        <a href="{{ route('karyawan.promo.create') }}" class="btn-tambah">+ Tambah</a>
    </div>
</div>

<div class="promo-list" id="promoList">
    @forelse($promos ?? [] as $promo)
    <div class="promo-card" data-search="{{ strtolower($promo->produk->nama ?? '') }}">
        <div class="promo-img">
            @if($promo->produk->gambar ?? null)
                <img src="{{ asset('storage/' . $promo->produk->gambar) }}" alt="{{ $promo->produk->nama }}">
            @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;background:var(--pink-light);border-radius:12px;">🧥</div>
            @endif
        </div>
        <div class="promo-info" style="flex:1;">
            <h3>Promo Untuk {{ $promo->produk->nama ?? 'Produk' }}</h3>
            <p>Diskon: {{ $promo->diskon }}%</p>
            <p>Tanggal Mulai - Berakhir:
                {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                Sampai Di
                {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->format('d M Y') }}
            </p>
            <p>Status:
                <span class="{{ $promo->status == 'Aktif' ? 'promo-status-aktif' : 'status-proses' }}">
                    {{ $promo->status }}
                </span>
            </p>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('karyawan.promo.edit', $promo->id) }}"
                style="background:var(--pink-btn);color:white;border:none;border-radius:8px;padding:8px 16px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;text-align:center;text-decoration:none;font-size:13px;">
                Edit
            </a>
            <form action="{{ route('karyawan.promo.destroy', $promo->id) }}" method="POST"
                onsubmit="return confirm('Hapus promo ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                    style="width:100%;background:#fee2e2;border:none;border-radius:8px;padding:8px 16px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;color:#dc2626;font-size:13px;">
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    {{-- Dummy --}}
    @for($i = 0; $i < 3; $i++)
    <div class="promo-card">
        <div class="promo-img">
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;background:var(--pink-light);border-radius:12px;">🧥</div>
        </div>
        <div class="promo-info">
            <h3>Promo Untuk Produk A</h3>
            <p>Diskon: 30%</p>
            <p>Tanggal Mulai - Berakhir: 22 Mei 2026 Sampai Di 30 Mei 2026</p>
            <p>Status: <span class="promo-status-aktif">Aktif</span></p>
        </div>
    </div>
    @endfor
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function filterPromo(val) {
    const cards = document.querySelectorAll('#promoList .promo-card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(val.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush