@extends('layouts.karyawan')

@section('title', 'Dashboard')

@section('content')

<h2 class="dashboard-section-title">Detail Penjualan Hari Ini</h2>

{{-- STATS CARDS --}}
<div class="stats-grid mb-24">
    <div class="stat-card">
        <div class="stat-icon"><img src="{{ asset('images/penjualan-hari-ini.png') }}" style="width:48px;height:48px;object-fit:contain;" alt="Penjualan"></div>
        <div class="stat-label">Penjualan Hari Ini</div>
        <div>
            <span class="stat-value">{{ $penjualanHariIni }}</span>
            <span class="stat-sub"> transaksi</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><img src="{{ asset('images/total-transaksi.png') }}" style="width:48px;height:48px;object-fit:contain;" alt="Transaksi"></div>
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">Rp{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
        <div class="stat-sub">hari ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><img src="{{ asset('images/stok-menipis.png') }}" style="width:48px;height:48px;object-fit:contain;" alt="Stok Menipis"></div>
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value">{{ $stokMenipis }} <span class="stat-sub">produk</span></div>
    </div>
</div>

{{-- PESANAN MASUK & STOK MENIPIS --}}
<div class="dashboard-grid mb-24">
    <div>
        <h2 class="dashboard-section-title">Pesanan Masuk</h2>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Id Pesanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesananMasuk as $pesanan)
                    <tr>
                        <td>{{ $pesanan->kode_pesanan }}</td>
                        <td>{{ $pesanan->created_at->format('d-m-Y') }}</td>
                        <td>
                            <span class="status-{{ str_replace('_', '', $pesanan->status_pesanan) }}">
                                {{ ucwords(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:#bbb;padding:14px">
                            Belum ada pesanan masuk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="dashboard-section-title">Stok Menipis</h2>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Sisa Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokMenipisData as $stok)
                    <tr>
                        <td>{{ $stok->produk->nama_produk ?? '-' }}</td>
                        <td>{{ $stok->stok }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align:center;color:#bbb;padding:14px">
                            Stok aman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- DETAIL PENJUALAN HARI INI --}}
<h2 class="dashboard-section-title">Detail Penjualan Hari Ini</h2>
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Id Transaksi</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiHariIni as $transaksi)
            <tr>
                <td>{{ $transaksi->kode_transaksi }}</td>
                <td>{{ $transaksi->created_at->format('d-m-Y') }}</td>
                <td>Rp{{ number_format($transaksi->total, 0, ',', '.') }}</td>
                <td>{{ ucfirst($transaksi->metode_bayar) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#bbb;padding:14px">
                    Belum ada transaksi hari ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection