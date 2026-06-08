@extends('layouts.karyawan')

@section('title', 'Riwayat')

@section('content')

<div class="page-header">
    <h1 class="page-title">Riwayat</h1>
    <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
        </svg>
        <input type="text" placeholder="cari..." id="searchRiwayat" oninput="filterRiwayat(this.value)">
    </div>
</div>

{{-- Tab Online / Offline / Semua --}}
<div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    @foreach(['semua' => 'Semua', 'online' => 'Online', 'offline' => 'Offline'] as $val => $label)
    <a href="{{ request()->fullUrlWithQuery(['tipe' => $val]) }}"
       style="padding:8px 20px; border-radius:20px; font-weight:700; font-size:13px; text-decoration:none;
              background:{{ $tipe === $val ? 'var(--pink-btn)' : 'var(--pink-light)' }};
              color:{{ $tipe === $val ? 'white' : 'var(--text-dark)' }};">
        {{ $label }}
    </a>
    @endforeach
</div>

<div id="riwayatList">

    {{-- PESANAN ONLINE --}}
    @if(in_array($tipe, ['semua', 'online']))
        @if($pesanans instanceof \Illuminate\Contracts\Pagination\Paginator ? $pesanans->isNotEmpty() : $pesanans->isNotEmpty())
            @foreach($pesanans as $pesanan)
            <div class="pesanan-card" data-text="{{ strtolower($pesanan->kode_pesanan . ' ' . ($pesanan->pembeli?->nama ?? '')) }}">
                <div class="pesanan-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <span>#{{ $pesanan->kode_pesanan }}</span>
                    <span style="font-size:11px;background:#e0f2fe;color:#0369a1;padding:2px 10px;border-radius:20px;">Online</span>
                </div>
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
                        <div class="pesanan-info-label">Tanggal:</div>
                        <div class="pesanan-info-value">{{ $pesanan->created_at->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Metode:</div>
                        <div class="pesanan-info-value">
                            {{ $pesanan->pembayaran ? ucfirst(str_replace('_', ' ', $pesanan->pembayaran->metode)) : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Status:</div>
                        <div class="pesanan-info-value"><span class="status-selesai">Selesai</span></div>
                    </div>
                </div>
                <div class="pesanan-info-label" style="margin-bottom:10px;">Produk:</div>
                <div class="pesanan-products">
                    @foreach($pesanan->details->take(5) as $detail)
                    <div class="pesanan-product-img">
                        @if($detail->varian?->produk?->foto)
                            <img src="{{ asset('storage/' . $detail->varian->produk->foto) }}"
                                 alt="{{ $detail->varian->produk->nama_produk }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                        @else
                            <div style="width:100%;height:100%;background:var(--pink-light);display:flex;align-items:center;justify-content:center;font-size:28px;border-radius:12px;">📦</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:10px;">
                    <a href="{{ route('karyawan.riwayat.pesanan', $pesanan->id) }}"
                       style="font-size:12px;color:var(--pink-btn);font-weight:700;text-decoration:none;">Lihat Detail →</a>
                </div>
            </div>
            @endforeach
            @if($pesanans instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div style="margin:16px 0;">{{ $pesanans->links() }}</div>
            @endif
        @elseif($tipe !== 'semua')
            <div style="text-align:center;padding:40px;color:#aaa;">Belum ada riwayat pesanan online.</div>
        @endif
    @endif

    {{-- TRANSAKSI OFFLINE --}}
    @if(in_array($tipe, ['semua', 'offline']))
        @if($transaksis instanceof \Illuminate\Contracts\Pagination\Paginator ? $transaksis->isNotEmpty() : $transaksis->isNotEmpty())
            @foreach($transaksis as $transaksi)
            <div class="pesanan-card" data-text="{{ strtolower($transaksi->kode_transaksi) }}">
                <div class="pesanan-card-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <span>#{{ $transaksi->kode_transaksi }}</span>
                    <span style="font-size:11px;background:#fef3c7;color:#92400e;padding:2px 10px;border-radius:20px;">Offline</span>
                </div>
                <div class="pesanan-info-grid">
                    <div>
                        <div class="pesanan-info-label">Karyawan:</div>
                        <div class="pesanan-info-value">{{ $transaksi->karyawan?->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Total:</div>
                        <div class="pesanan-info-value">Rp{{ number_format($transaksi->total, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Tanggal:</div>
                        <div class="pesanan-info-value">{{ $transaksi->created_at->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Metode:</div>
                        <div class="pesanan-info-value">{{ ucfirst($transaksi->metode_bayar) }}</div>
                    </div>
                    <div>
                        <div class="pesanan-info-label">Status:</div>
                        <div class="pesanan-info-value"><span class="status-selesai">Selesai</span></div>
                    </div>
                </div>
                <div style="margin-top:10px;">
                    <a href="{{ route('karyawan.riwayat.transaksi', $transaksi->id) }}"
                       style="font-size:12px;color:var(--pink-btn);font-weight:700;text-decoration:none;">Lihat Detail →</a>
                </div>
            </div>
            @endforeach
            @if($transaksis instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div style="margin:16px 0;">{{ $transaksis->links() }}</div>
            @endif
        @elseif($tipe !== 'semua')
            <div style="text-align:center;padding:40px;color:#aaa;">Belum ada riwayat transaksi offline.</div>
        @endif
    @endif

    @if(
        ($tipe === 'semua') &&
        (($pesanans instanceof \Illuminate\Contracts\Pagination\Paginator ? $pesanans->isEmpty() : $pesanans->isEmpty())) &&
        (($transaksis instanceof \Illuminate\Contracts\Pagination\Paginator ? $transaksis->isEmpty() : $transaksis->isEmpty()))
    )
    <div style="text-align:center; padding:60px 20px; color:#aaa;">
        <div style="font-size:48px; margin-bottom:16px;">🕐</div>
        <p style="font-size:16px; font-weight:600;">Belum ada riwayat transaksi</p>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
function filterRiwayat(val) {
    const cards = document.querySelectorAll('#riwayatList .pesanan-card');
    cards.forEach(card => {
        const text = (card.dataset.text || '') + card.textContent.toLowerCase();
        card.style.display = text.includes(val.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush