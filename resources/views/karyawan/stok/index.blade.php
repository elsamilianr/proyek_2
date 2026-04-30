@extends('layouts.karyawan')

@section('title', 'Stok')

@section('content')

<div class="page-header">
    <h1 class="page-title">Stok</h1>
    <div style="display:flex;gap:12px;align-items:center;">
        <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
            </svg>
            <input type="text" placeholder="cari..." id="searchStok" oninput="filterStok(this.value)">
        </div>
    </div>
</div>

<div class="table-card">
    <table class="data-table" id="stokTable">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Warna</th>
                <th>Size</th>
                <th>Stok</th>
                <th>Tanggal Terakhir Restok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($varians ?? [] as $varian)
            <tr data-search="{{ strtolower($varian->produk->nama ?? '') }}">
                <td>{{ $varian->produk->nama ?? '-' }}</td>
                <td>{{ $varian->warna }}</td>
                <td>{{ $varian->ukuran }}</td>
                <td>{{ $varian->stok }}</td>
                <td>{{ $varian->updated_at ? \Carbon\Carbon::parse($varian->updated_at)->format('d-m-Y') : '-' }}</td>
                <td>
                    <button onclick="openEditModal({{ $varian->id }}, {{ $varian->stok }})" class="btn-edit">Edit</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:var(--text-gray);padding:24px;">Belum ada data stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah Stok --}}
<div id="editModal" class="modal-overlay" style="display:none;" onclick="closeModal(event)">
    <div class="modal-box" onclick="event.stopPropagation()" style="max-width:360px;">
        <div class="modal-title">Tambah Stok</div>
        <form id="editForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label class="form-label">Jumlah Tambah Stok</label>
                <input class="form-input" type="number" name="tambah" min="1" value="1" required>
            </div>
            <button type="submit" class="form-btn" style="margin-top:16px;">Simpan</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterStok(val) {
    document.querySelectorAll('#stokTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
    });
}

function openEditModal(varianId, stokSaat) {
    document.getElementById('editForm').action = '/karyawan/varian/' + varianId + '/tambah-stok';
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(e) {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endpush