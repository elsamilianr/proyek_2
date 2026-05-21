@extends('layouts.karyawan')

@section('title', 'Tambah Promo')

@section('content')

<div class="form-page">
    <h2 class="form-title">formulir tambah promo</h2>

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <ul style="list-style:none;padding:0;">
            @foreach($errors->all() as $error)
            <li style="color:#dc2626;font-weight:600;font-size:14px;">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('karyawan.promo.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">nama promo</label>
            <input class="form-input" type="text" name="nama_promo"
                placeholder="contoh: Promo Lebaran 2025"
                value="{{ old('nama_promo') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">deskripsi</label>
            <textarea class="form-input" name="deskripsi" rows="2"
                placeholder="deskripsi singkat promo (opsional)"
                style="resize:vertical;">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">tipe diskon</label>
            <select class="form-input" name="tipe_diskon" required style="cursor:pointer;" onchange="updateDiskonLabel(this.value)">
                <option value="persen"  {{ old('tipe_diskon','persen') == 'persen'  ? 'selected' : '' }}>Persen (%)</option>
                <option value="nominal" {{ old('tipe_diskon') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">diskon (<span id="diskonSatuan">%</span>)</label>
            <input class="form-input" type="number" name="diskon"
                placeholder="contoh: 10" min="0"
                value="{{ old('diskon') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">minimal pembelian (Rp)</label>
            <input class="form-input" type="number" name="min_pembelian"
                placeholder="kosongkan jika tidak ada" min="0"
                value="{{ old('min_pembelian') }}">
        </div>

        {{-- Pilih Produk Multi --}}
        <div class="form-group">
            <label class="form-label">produk yang berlaku</label>
            <p style="font-size:12px;color:#aaa;margin-bottom:10px;">
                Kosongkan semua centang jika promo berlaku untuk semua produk.
            </p>

            {{-- Tag produk terpilih --}}
            <div id="selectedTags" style="display:flex;flex-wrap:wrap;gap:6px;min-height:0;margin-bottom:10px;"></div>

            {{-- Search produk --}}
            <input class="form-input" type="text" id="searchProduk"
                placeholder="cari nama produk..."
                oninput="filterCheckbox(this.value)" onfocus="filterCheckbox(this.value)"
                autocomplete="off"
                style="margin-bottom:10px;">

            {{-- Daftar checkbox --}}
            <div id="produkCheckboxList" style="
                display:none;
                background:#fff;
                border:1px solid #eee;
                border-radius:12px;
                max-height:220px;
                overflow-y:auto;
                padding:8px;
            ">
                @forelse($produks ?? [] as $produk)
                <label class="produk-checkbox-item"
                    data-nama="{{ strtolower($produk->nama_produk) }}"
                    style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;cursor:pointer;">
                    <input type="checkbox"
                        name="produk_ids[]"
                        value="{{ $produk->id }}"
                        id="produk_{{ $produk->id }}"
                        {{ in_array($produk->id, old('produk_ids', [])) ? 'checked' : '' }}
                        onchange="updateTags()"
                        style="width:16px;height:16px;accent-color:var(--pink-primary);cursor:pointer;">
                    <span style="font-size:14px;font-weight:600;color:var(--text-dark);">{{ $produk->nama_produk }}</span>
                </label>
                @empty
                <div style="padding:12px;font-size:13px;color:#aaa;text-align:center;">Belum ada produk aktif.</div>
                @endforelse
                <div id="checkboxEmpty" style="display:none;padding:12px;font-size:13px;color:#aaa;text-align:center;">Produk tidak ditemukan</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">tanggal mulai</label>
            <input class="form-input" type="date" name="tanggal_mulai"
                value="{{ old('tanggal_mulai') }}" required style="cursor:pointer;">
        </div>

        <div class="form-group">
            <label class="form-label">tanggal selesai</label>
            <input class="form-input" type="date" name="tanggal_selesai"
                value="{{ old('tanggal_selesai') }}" required style="cursor:pointer;">
        </div>

        <button type="submit" class="form-btn">Simpan Promo</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
function updateDiskonLabel(val) {
    document.getElementById('diskonSatuan').textContent = val === 'persen' ? '%' : 'Rp';
}

function filterCheckbox(keyword) {
    const items = document.querySelectorAll('.produk-checkbox-item');
    const empty = document.getElementById('checkboxEmpty');
    const list  = document.getElementById('produkCheckboxList');
    const q     = keyword.toLowerCase().trim();
    let ada     = false;

    if (q === '') {
        list.style.display = 'none';
        return;
    }

    list.style.display = 'block';
    items.forEach(item => {
        const cocok = item.dataset.nama.includes(q);
        item.style.display = cocok ? 'flex' : 'none';
        if (cocok) ada = true;
    });

    empty.style.display = ada ? 'none' : 'block';
}

function updateTags() {
    const container = document.getElementById('selectedTags');
    const checked = document.querySelectorAll('input[name="produk_ids[]"]:checked');
    container.innerHTML = '';

    checked.forEach(cb => {
        const label = cb.closest('label').querySelector('span').textContent;
        const tag = document.createElement('div');
        tag.style.cssText = 'display:inline-flex;align-items:center;gap:6px;background:var(--pink-light);border:1px solid var(--pink-card);border-radius:999px;padding:4px 12px;font-size:13px;font-weight:700;color:var(--text-dark);';
        tag.innerHTML = `${label} <span onclick="hapusTag(${cb.value})" style="cursor:pointer;color:#ec4899;font-weight:900;font-size:15px;line-height:1;">×</span>`;
        container.appendChild(tag);
    });
}

function hapusTag(id) {
    const cb = document.getElementById('produk_' + id);
    if (cb) { cb.checked = false; updateTags(); }
}

// Hover style pada checkbox item
document.querySelectorAll('.produk-checkbox-item').forEach(item => {
    item.addEventListener('mouseenter', () => item.style.background = '#fdf2f8');
    item.addEventListener('mouseleave', () => item.style.background = '');
});

// Inisialisasi tag jika ada old() value
updateTags();
</script>
@endpush