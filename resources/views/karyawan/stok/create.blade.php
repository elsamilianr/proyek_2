@extends('layouts.karyawan')

@section('title', 'Tambah Stok')

@section('content')

<div class="form-page">
    <h2 class="form-title">formulir tambah stok</h2>

    @if($errors->any())
    <div style="background:#fee2e2;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
        <ul style="list-style:none;padding:0;">
            @foreach($errors->all() as $error)
            <li style="color:#dc2626;font-weight:600;font-size:14px;">• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('karyawan.stok.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">nama produk</label>
            <select class="form-input" name="produk_id" required style="cursor:pointer;">
                <option value="" disabled selected>masukkan nama produk</option>
                @foreach($produks ?? [] as $produk)
                <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                    {{ $produk->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">warna produk</label>
            <input class="form-input" type="text" name="warna" placeholder="masukkan warna produk"
                value="{{ old('warna') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">size produk</label>
            <input class="form-input" type="text" name="size" placeholder="masukkan size produk"
                value="{{ old('size') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">stok produk</label>
            <input class="form-input" type="number" name="jumlah" placeholder="masukkan stok produk"
                value="{{ old('jumlah') }}" min="0" required>
        </div>

        <div class="form-group">
            <label class="form-label">tanggal terakhir restok</label>
            <input class="form-input" type="date" name="tanggal_restok"
                value="{{ old('tanggal_restok', date('Y-m-d')) }}" required
                style="cursor:pointer;">
        </div>

        <button type="submit" class="form-btn">Simpan Stok</button>
    </form>
</div>

@endsection