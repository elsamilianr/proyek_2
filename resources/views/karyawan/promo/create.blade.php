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
            <label class="form-label">produk</label>
            <select class="form-input" name="produk_id" required style="cursor:pointer;">
                <option value="" disabled selected>pilih produk</option>
                @foreach($produks ?? [] as $produk)
                <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                    {{ $produk->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">diskon (%)</label>
            <input class="form-input" type="number" name="diskon" placeholder="masukkan diskon (%)"
                value="{{ old('diskon') }}" min="1" max="100" required>
        </div>

        <div class="form-group">
            <label class="form-label">tanggal mulai</label>
            <input class="form-input" type="date" name="tanggal_mulai"
                value="{{ old('tanggal_mulai') }}" required style="cursor:pointer;">
        </div>

        <div class="form-group">
            <label class="form-label">tanggal berakhir</label>
            <input class="form-input" type="date" name="tanggal_berakhir"
                value="{{ old('tanggal_berakhir') }}" required style="cursor:pointer;">
        </div>

        <div class="form-group">
            <label class="form-label">status</label>
            <select class="form-input" name="status" required style="cursor:pointer;">
                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <button type="submit" class="form-btn">Simpan Promo</button>
    </form>
</div>

@endsection