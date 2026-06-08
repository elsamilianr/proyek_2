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
            <tr data-search="{{ strtolower($varian->produk->nama_produk ?? '') }}">
                <td>{{ $varian->produk->nama_produk ?? '-' }}</td>
                <td>{{ $varian->warna ?? '-' }}</td>
                <td>{{ $varian->size ?? '-' }}</td>
                <td>{{ $varian->stok }}</td>
                <td>{{ $varian->updated_at ? \Carbon\Carbon::parse($varian->updated_at)->format('d-m-Y') : '-' }}</td>
                <td>
                    <button onclick="openEditModal({{ $varian->id }}, '{{ addslashes($varian->produk->nama_produk ?? '-') }}', '{{ addslashes(implode(' / ', array_filter([$varian->warna ?? null, $varian->size ?? null]))) }}', {{ $varian->stok }})" class="btn-edit">Edit</button>
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
    <div class="modal-box" onclick="event.stopPropagation()" style="max-width:340px;">
        <div class="modal-title">Tambah Stok</div>

        {{-- Info produk --}}
        <div style="background:var(--white); border-radius:12px; padding:12px 16px; margin-bottom:20px;">
            <div id="modalNamaProduk" style="font-weight:700; font-size:14px; color:var(--text-dark);"></div>
            <div id="modalDetailProduk" style="font-size:12px; color:var(--text-gray); margin-top:2px;"></div>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label class="form-label">Jumlah Tambah Stok</label>
                <input id="stokInput" class="form-input" type="number" name="jumlah" min="1" value="1" required
                    style="text-align:left; appearance:none; -moz-appearance:textfield;
                           background:var(--white); height:52px; line-height:52px;
                           padding:0 22px; font-size:18px; font-weight:700; color:var(--text-dark);
                           border:2px solid var(--pink-border);">
            </div>
            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="button" onclick="closeModal()"
                    style="flex:1; background:var(--white); border:1.5px solid var(--pink-card);
                           border-radius:50px; padding:12px; font-family:'Nunito',sans-serif;
                           font-weight:700; font-size:14px; cursor:pointer; color:var(--text-gray);">
                    Batal
                </button>
                <button type="submit" class="form-btn" style="flex:2; margin-top:0; padding:12px;">
                    Simpan
                </button>
            </div>
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

function openEditModal(varianId, nama, detail, stok) {
    document.getElementById('editForm').action = '/karyawan/varian/' + varianId + '/tambah-stok';
    document.getElementById('stokInput').value = 1;
    document.getElementById('modalNamaProduk').textContent = nama;
    document.getElementById('modalDetailProduk').textContent = detail + ' · Stok saat ini: ' + stok;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(e) {
    if (!e || e.target === document.getElementById('editModal')) {
        document.getElementById('editModal').style.display = 'none';
    }
}
</script>
@endpush