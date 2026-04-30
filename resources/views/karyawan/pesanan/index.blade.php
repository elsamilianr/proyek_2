@extends('layouts.karyawan')

@section('title', 'Pesanan')

@section('content')

<div class="page-header">
    <h1 class="page-title">Pesanan</h1>
    <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
        </svg>
        <input type="text" placeholder="cari..." id="searchPesanan" oninput="filterPesanan(this.value)">
    </div>
</div>

<div id="pesananList">
    @forelse($pesanans ?? [] as $pesanan)
    <div class="pesanan-card" data-id="{{ $pesanan->id }}">
        <div class="pesanan-card-header">#Pesanan{{ $pesanan->id }}</div>
        <div class="pesanan-info-grid">
            <div>
                <div class="pesanan-info-label">Total:</div>
                <div class="pesanan-info-value">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="pesanan-info-label">Tanggal Pesan:</div>
                <div class="pesanan-info-value">
                    {{ \Carbon\Carbon::parse($pesanan->tanggal)->translatedFormat('d F Y') }}
                </div>
            </div>
            <div>
                <div class="pesanan-info-label">Metode Pembayaran:</div>
                <div class="pesanan-info-value">{{ $pesanan->metode_pembayaran }}</div>
            </div>
            <div>
                <div class="pesanan-info-label">Status Pesanan:</div>
                <div class="pesanan-info-value">
                    @if($pesanan->status == 'Dalam Proses')
                        <span class="status-proses">Dalam Proses</span>
                    @elseif($pesanan->status == 'Selesai')
                        <span class="status-selesai">Selesai</span>
                    @else
                        <span class="status-menunggu">{{ $pesanan->status }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pesanan-info-label" style="margin-bottom:10px;">Produk:</div>
        <div class="pesanan-products">
            @foreach($pesanan->items ?? [] as $item)
            <div class="pesanan-product-img">
                @if($item->produk->gambar ?? null)
                    <img src="{{ asset('storage/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama }}">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;">🧥</div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Update status --}}
        @if($pesanan->status != 'Selesai')
        <form action="{{ route('karyawan.pesanan.update', $pesanan->id) }}" method="POST" style="margin-top:12px;">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="Selesai">
            <button type="submit"
                style="background:var(--green-btn);color:white;border:none;border-radius:8px;padding:8px 20px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;">
                Tandai Selesai
            </button>
        </form>
        @endif
    </div>
    @empty
    {{-- Dummy --}}
    <div class="pesanan-card">
        <div class="pesanan-card-header">#Pesanan423526</div>
        <div class="pesanan-info-grid">
            <div>
                <div class="pesanan-info-label">Total:</div>
                <div class="pesanan-info-value">Rp796.000</div>
            </div>
            <div>
                <div class="pesanan-info-label">Tanggal Pesan:</div>
                <div class="pesanan-info-value">30 September 2025</div>
            </div>
            <div>
                <div class="pesanan-info-label">Metode Pembayaran:</div>
                <div class="pesanan-info-value">Qris</div>
            </div>
            <div>
                <div class="pesanan-info-label">Status Pesanan:</div>
                <div class="pesanan-info-value"><span class="status-proses">Dalam Proses</span></div>
            </div>
        </div>
        <div class="pesanan-info-label" style="margin-bottom:10px;">Produk:</div>
        <div class="pesanan-products">
            @for($i = 0; $i < 4; $i++)
            <div class="pesanan-product-img">
                <div style="width:100%;height:100%;background:var(--pink-light);display:flex;align-items:center;justify-content:center;font-size:28px;border-radius:12px;">🧥</div>
            </div>
            @endfor
        </div>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function filterPesanan(val) {
    const cards = document.querySelectorAll('#pesananList .pesanan-card');
    cards.forEach(card => {
        const id = (card.dataset.id || '').toString();
        const text = card.textContent.toLowerCase();
        card.style.display = (text.includes(val.toLowerCase()) || id.includes(val)) ? '' : 'none';
    });
}
</script>
@endpush