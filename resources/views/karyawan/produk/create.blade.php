@extends('layouts.karyawan')

@section('title', 'Tambah Produk')

@section('content')

<div class="form-page">
    <h2 class="form-title">formulir tambah produk</h2>

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <ul style="list-style:none;padding:0;">
            @foreach($errors->all() as $error)
            <li style="color:#dc2626;font-weight:600;font-size:14px;">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('karyawan.produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Info Produk --}}
        <div class="form-group">
            <label class="form-label">nama produk</label>
            <input class="form-input" type="text" name="nama_produk" placeholder="masukkan nama produk"
                value="{{ old('nama_produk') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">kategori</label>
            <input class="form-input" type="text" name="kategori" placeholder="contoh: hijab, gamis, aksesoris"
                value="{{ old('kategori') }}">
        </div>

        <div class="form-group">
            <label class="form-label">deskripsi produk</label>
            <textarea class="form-input" name="deskripsi" rows="3"
                placeholder="deskripsi singkat produk (opsional)"
                style="resize:vertical;">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">foto utama produk</label>
            <input class="form-input" type="file" name="foto" accept="image/*"
                style="cursor:pointer;" onchange="previewGambar(this)">
            <div id="preview-gambar" style="margin-top:12px;display:none;">
                <img id="gambar-preview" src="" alt="Preview"
                    style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:2px solid var(--pink-card);">
            </div>
        </div>

        {{-- Varian (minimal 1) --}}
        <div style="margin-top:24px;margin-bottom:12px;border-top:1px solid #eee;padding-top:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <label class="form-label" style="margin:0;">varian produk <span style="color:#dc2626;">*</span></label>
                <button type="button" onclick="tambahVarian()"
                    style="background:var(--pink-light);border:none;border-radius:8px;padding:6px 14px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;font-size:13px;">
                    + Tambah Varian
                </button>
            </div>
            <p style="font-size:12px;color:#aaa;margin-bottom:16px;">Setiap produk harus memiliki minimal 1 varian. Warna dan size boleh dikosongkan.</p>

            <div id="varianContainer">
                {{-- Varian pertama (tidak bisa dihapus) --}}
                <div class="varian-row" style="background:#fafafa;border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:12px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">warna</label>
                            <input class="form-input" type="text" name="varians[0][warna]"
                                placeholder="contoh: Hitam" value="{{ old('varians.0.warna') }}"
                                style="margin-bottom:0;">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px;">size</label>
                            <input class="form-input" type="text" name="varians[0][size]"
                                placeholder="contoh: M, L, XL" value="{{ old('varians.0.size') }}"
                                style="margin-bottom:0;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">harga <span style="color:#dc2626;">*</span></label>
                            <input class="form-input" type="number" name="varians[0][harga]"
                                placeholder="contoh: 150000" min="0" value="{{ old('varians.0.harga') }}"
                                style="margin-bottom:0;" required>
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px;">stok <span style="color:#dc2626;">*</span></label>
                            <input class="form-input" type="number" name="varians[0][stok]"
                                placeholder="contoh: 10" min="0" value="{{ old('varians.0.stok') }}"
                                style="margin-bottom:0;" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="form-btn">Simpan Produk</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
let varianIndex = 1;

function previewGambar(input) {
    const preview = document.getElementById('preview-gambar');
    const img = document.getElementById('gambar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function tambahVarian() {
    const i = varianIndex++;
    const container = document.getElementById('varianContainer');
    const div = document.createElement('div');
    div.className = 'varian-row';
    div.style.cssText = 'background:#fafafa;border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:12px;position:relative;';
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()"
            style="position:absolute;top:10px;right:12px;background:#fee2e2;border:none;border-radius:6px;
                   padding:2px 8px;font-size:12px;color:#dc2626;font-weight:700;cursor:pointer;">✕</button>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div>
                <label class="form-label" style="font-size:12px;">warna</label>
                <input class="form-input" type="text" name="varians[${i}][warna]" placeholder="contoh: Hitam" style="margin-bottom:0;">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">size</label>
                <input class="form-input" type="text" name="varians[${i}][size]" placeholder="contoh: M, L, XL" style="margin-bottom:0;">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label" style="font-size:12px;">harga *</label>
                <input class="form-input" type="number" name="varians[${i}][harga]" placeholder="contoh: 150000" min="0" style="margin-bottom:0;" required>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">stok *</label>
                <input class="form-input" type="number" name="varians[${i}][stok]" placeholder="contoh: 10" min="0" style="margin-bottom:0;" required>
            </div>
        </div>
    `;
    container.appendChild(div);
}
</script>
@endpush
