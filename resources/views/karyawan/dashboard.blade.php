@extends('layouts.karyawan')

@section('title', 'Dashboard')

@section('content')

<h2 class="dashboard-section-title">Detail Penjualan Hari Ini</h2>

{{-- STATS CARDS --}}
<div class="stats-grid mb-24">
    <div class="stat-card">
        <div class="stat-icon">🎁</div>
        <div class="stat-label">Penjualan Hari Ini</div>
        <div>
            <span class="stat-value">{{ $penjualanHariIni ?? 10 }}</span>
            <span class="stat-sub"> transaksi</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👜</div>
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">Rp{{ number_format($totalTransaksi ?? 1500000, 0, ',', '.') }}</div>
        <div class="stat-sub">hari ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⚠️</div>
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value">{{ $stokMenipis ?? 3 }} <span class="stat-sub">produk</span></div>
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
                    @forelse($pesananMasuk ?? [] as $pesanan)
                    <tr>
                        <td>{{ $pesanan->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d-m-Y') }}</td>
                        <td>
                            <span class="status-{{ strtolower(str_replace(' ', '', $pesanan->status)) }}">
                                {{ $pesanan->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    {{-- Sample data for display --}}
                    <tr>
                        <td>423526</td>
                        <td>22-02-2026</td>
                        <td><span class="status-menunggu">Menunggu</span></td>
                    </tr>
                    <tr>
                        <td>423526</td>
                        <td>22-02-2026</td>
                        <td><span class="status-menunggu">Menunggu</span></td>
                    </tr>
                    <tr>
                        <td>423526</td>
                        <td>22-02-2026</td>
                        <td><span class="status-menunggu">Menunggu</span></td>
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
                    @forelse($stokMenipisData ?? [] as $stok)
                    <tr>
                        <td>{{ $stok->produk->nama ?? '-' }}</td>
                        <td>{{ $stok->jumlah }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
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
            @forelse($transaksiHariIni ?? [] as $transaksi)
            <tr>
                <td>{{ $transaksi->id }}</td>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y') }}</td>
                <td>Rp{{ number_format($transaksi->total, 0, ',', '.') }}</td>
                <td>{{ $transaksi->metode_pembayaran }}</td>
            </tr>
            @empty
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection