@extends('layouts.karyawan')

@section('title', 'Edit Produk')

@section('content')

<div class="form-page">
    <h2 class="form-title">formulir edit produk</h2>

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <ul style="list-style:none;padding:0;">
            @foreach($errors->all() as $error)
            <li style="color:#dc2626;font-weight:600;font-size:14px;">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('karyawan.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">nama produk</label>
            <input class="form-input" type="text" name="nama" placeholder="masukkan nama produk"
                value="{{ old('nama', $produk->nama) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">gambar produk</label>
            @if($produk->gambar)
            <div style="margin-bottom:10px;">
                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}"
                    style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:2px solid var(--pink-card);">
            </div>
            @endif
            <input class="form-input" type="file" name="gambar" accept="image/*" style="cursor:pointer;">
        </div>

        <div class="form-group">
            <label class="form-label">harga produk</label>
            <input class="form-input" type="number" name="harga" placeholder="masukkan harga produk"
                value="{{ old('harga', $produk->harga) }}" min="0" required>
        </div>

        <div class="form-group">
            <label class="form-label">size produk</label>
            <input class="form-input" type="text" name="size" placeholder="masukkan size produk"
                value="{{ old('size', $produk->size) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">warna produk</label>
            <input class="form-input" type="text" name="warna" placeholder="masukkan warna produk"
                value="{{ old('warna', $produk->warna) }}" required>
        </div>

        <button type="submit" class="form-btn">Update Produk</button>
    </form>
</div>

@endsection