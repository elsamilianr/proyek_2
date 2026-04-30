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

        <div class="form-group">
            <label class="form-label">nama produk</label>
            <input class="form-input" type="text" name="nama" placeholder="masukkan nama produk"
                value="{{ old('nama') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">gambar produk</label>
            <input class="form-input" type="file" name="gambar" accept="image/*"
                style="cursor:pointer;" onchange="previewGambar(this)">
            <div id="preview-gambar" style="margin-top:12px;display:none;">
                <img id="gambar-preview" src="" alt="Preview"
                    style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:2px solid var(--pink-card);">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">harga produk</label>
            <input class="form-input" type="number" name="harga" placeholder="masukkan harga produk"
                value="{{ old('harga') }}" min="0" required>
        </div>

        <div class="form-group">
            <label class="form-label">size produk</label>
            <input class="form-input" type="text" name="size" placeholder="masukkan size produk"
                value="{{ old('size') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">warna produk</label>
            <input class="form-input" type="text" name="warna" placeholder="masukkan warna produk"
                value="{{ old('warna') }}" required>
        </div>

        <button type="submit" class="form-btn">Simpan Produk</button>
    </form>
</div>

@endsection

@push('scripts')
<script>
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
</script>
@endpush