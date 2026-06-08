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
    @forelse($pesanans as $pesanan)
    <div class="pesanan-card" data-kode="{{ strtolower($pesanan->kode_pesanan) }}">
        <div class="pesanan-card-header">#{{ $pesanan->kode_pesanan }}</div>
        <div class="pesanan-info-grid">
            <div>
                <div class="pesanan-info-label">Pembeli:</div>
                <div class="pesanan-info-value">{{ $pesanan->pembeli?->nama ?? '-' }}</div>
            </div>
            <div>
                <div class="pesanan-info-label">Total:</div>
                <div class="pesanan-info-value">Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="pesanan-info-label">Tanggal Pesan:</div>
                <div class="pesanan-info-value">{{ $pesanan->created_at->format('d M Y') }}</div>
            </div>
            <div>
                <div class="pesanan-info-label">Metode Pembayaran:</div>
                <div class="pesanan-info-value">
                    {{ $pesanan->pembayaran ? ucfirst(str_replace('_', ' ', $pesanan->pembayaran->metode)) : '-' }}
                </div>
            </div>
            <div>
                <div class="pesanan-info-label">Status Pesanan:</div>
                <div class="pesanan-info-value">
                    @php
                        $statusClass = match($pesanan->status_pesanan) {
                            'selesai'              => 'status-selesai',
                            'diproses'             => 'status-proses',
                            'dibatalkan'           => 'status-batal',
                            default                => 'status-menunggu',
                        };
                        $statusLabel = match($pesanan->status_pesanan) {
                            'menunggu_pembayaran'  => 'Menunggu Pembayaran',
                            'menunggu_verifikasi'  => 'Menunggu Verifikasi',
                            'diproses'             => 'Dalam Proses',
                            'selesai'              => 'Selesai',
                            'dibatalkan'           => 'Dibatalkan',
                            default                => ucfirst($pesanan->status_pesanan),
                        };
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
            </div>
        </div>

        {{-- Foto produk --}}
        <div class="pesanan-info-label" style="margin-bottom:10px;">Produk:</div>
        <div class="pesanan-products">
            @foreach($pesanan->details->take(5) as $detail)
            <div class="pesanan-product-img">
                @if($detail->varian?->produk?->foto)
                    <img src="{{ asset('storage/' . $detail->varian->produk->foto) }}"
                         alt="{{ $detail->varian->produk->nama_produk }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;background:var(--pink-light);border-radius:12px;">📦</div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Tombol aksi --}}
        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('karyawan.pesanan.show', $pesanan->id) }}"
               style="background:var(--pink-light);color:var(--text-dark);border:none;border-radius:8px;padding:8px 16px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;text-decoration:none;font-size:13px;">
                Lihat Detail
            </a>

            @if(in_array($pesanan->status_pesanan, ['menunggu_verifikasi', 'diproses']))
            <form action="{{ route('karyawan.pesanan.update-status', $pesanan->id) }}" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="selesai">
                <button type="submit" onclick="return confirm('Tandai pesanan ini selesai?')"
                    style="background:#d1fae5;color:#065f46;border:none;border-radius:8px;padding:8px 16px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;font-size:13px;">
                    Tandai Selesai
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding:60px 20px; color:#aaa;">
        <div style="font-size:48px; margin-bottom:16px;">📋</div>
        <p style="font-size:16px; font-weight:600;">Belum ada pesanan</p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function filterPesanan(val) {
    const cards = document.querySelectorAll('#pesananList .pesanan-card');
    cards.forEach(card => {
        const kode = card.dataset.kode || '';
        const text = card.textContent.toLowerCase();
        card.style.display = (text.includes(val.toLowerCase()) || kode.includes(val.toLowerCase())) ? '' : 'none';
    });
}
</script>
@endpush