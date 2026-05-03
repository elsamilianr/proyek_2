@extends('layouts.karyawan')

@section('title', 'Edit Produk')

@section('content')

<div class="form-page">
    <h2 class="form-title">formulir edit produk</h2>

    @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-weight:600;font-size:14px;">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <ul style="list-style:none;padding:0;">
            @foreach($errors->all() as $error)
            <li style="color:#dc2626;font-weight:600;font-size:14px;">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Edit Info Produk --}}
    <form action="{{ route('karyawan.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">nama produk</label>
            <input class="form-input" type="text" name="nama_produk" placeholder="masukkan nama produk"
                value="{{ old('nama_produk', $produk->nama_produk) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">kategori</label>
            <input class="form-input" type="text" name="kategori" placeholder="contoh: hijab, gamis, aksesoris"
                value="{{ old('kategori', $produk->kategori) }}">
        </div>

        <div class="form-group">
            <label class="form-label">deskripsi produk</label>
            <textarea class="form-input" name="deskripsi" rows="3"
                placeholder="deskripsi singkat produk (opsional)"
                style="resize:vertical;">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">foto utama produk</label>
            @if($produk->foto)
            <div style="margin-bottom:10px;">
                <img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->nama_produk }}"
                    style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--pink-card);">
                <p style="font-size:12px;color:#aaa;margin-top:4px;">Upload baru untuk mengganti foto</p>
            </div>
            @endif
            <input class="form-input" type="file" name="foto" accept="image/*" style="cursor:pointer;">
        </div>

        <div class="form-group">
            <label class="form-label">status produk</label>
            <select class="form-input" name="is_aktif" style="cursor:pointer;">
                <option value="1" {{ $produk->is_aktif ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$produk->is_aktif ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        {{-- Daftar Varian --}}
        <div style="margin-top:32px;border-top:1px solid #eee;padding-top:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:700;margin:0;">Varian Produk</h3>
                <button type="button" onclick="toggleTambahVarian()"
                    style="background:var(--pink-light);border:none;border-radius:8px;padding:6px 14px;font-family:'Nunito',sans-serif;font-weight:700;cursor:pointer;font-size:13px;">
                    + Tambah Varian
                </button>
            </div>

            {{-- Form tambah varian --}}
            <div id="formTambahVarian" style="display:none;background:#fafafa;border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:16px;">
                <form action="{{ route('karyawan.varian.store', $produk->id) }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">warna</label>
                            <input class="form-input" type="text" name="warna" placeholder="contoh: Hitam">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px;">size</label>
                            <input class="form-input" type="text" name="size" placeholder="contoh: M, L, XL">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">harga</label>
                            <input class="form-input" type="number" name="harga" min="0" required>
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px;">stok</label>
                            <input class="form-input" type="number" name="stok" min="0" required>
                        </div>
                    </div>

                    <button type="submit" class="form-btn" style="margin-top:12px;">Simpan Varian</button>
                </form>
            </div>

            {{-- List varian --}}
            @forelse($produk->varians as $varian)
            <div style="background:#fff;border:1px solid #eee;border-radius:12px;padding:14px 16px;margin-bottom:10px;">
                <form action="{{ route('karyawan.varian.update', $varian->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:10px;align-items:end;">
                        <input class="form-input" type="text" name="warna" value="{{ $varian->warna }}">
                        <input class="form-input" type="text" name="size" value="{{ $varian->size }}">
                        <input class="form-input" type="number" name="harga" value="{{ $varian->harga }}" min="0">
                        <input class="form-input" type="number" name="stok" value="{{ $varian->stok }}" min="0">

                        <button type="submit"
                            style="background:var(--pink-light);border:none;border-radius:8px;padding:8px 12px;font-weight:700;cursor:pointer;">
                            Simpan
                        </button>
                    </div>
                </form>

                <form action="{{ route('karyawan.varian.destroy', $varian->id) }}" method="POST"
                    onsubmit="return confirm('Hapus varian ini?')" style="margin-top:8px;text-align:right;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        style="background:#fee2e2;border:none;border-radius:6px;padding:4px 10px;font-size:11px;color:#dc2626;font-weight:700;cursor:pointer;">
                        Hapus Varian
                    </button>
                </form>
            </div>
            @empty
            <p style="color:#aaa;font-size:13px;text-align:center;padding:20px 0;">
                Belum ada varian. Klik <strong>+ Tambah Varian</strong>.
            </p>
            @endforelse
        </div>

        {{-- Tombol Update di paling bawah --}}
        <hr style="margin:30px 0;border:none;border-top:1px solid #eee;">

        <div style="text-align:right;">
            <button type="submit" class="form-btn">
                Update Produk
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
function toggleTambahVarian() {
    const form = document.getElementById('formTambahVarian');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush