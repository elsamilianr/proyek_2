@extends('layouts.karyawan')

@section('title', 'Edit Produk')

@section('content')

<div class="form-page">

    <h2 class="form-title">Form Edit Produk</h2>

    @if(session('success'))
        <div style="
            background:#d1fae5;
            color:#065f46;
            padding:12px 16px;
            border-radius:12px;
            margin-bottom:20px;
            font-weight:600;
        ">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="
            background:#fee2e2;
            color:#991b1b;
            padding:12px 16px;
            border-radius:12px;
            margin-bottom:20px;
        ">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('karyawan.produk.update', $produk->id) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="form-group">

            <label class="form-label">
                Nama Produk
            </label>

            <input
                type="text"
                class="form-input"
                name="nama_produk"
                value="{{ old('nama_produk', $produk->nama_produk) }}"
                required>

        </div>


        <div class="form-group">

            <label class="form-label">
                Kategori
            </label>

            <input
                type="text"
                class="form-input"
                name="kategori"
                value="{{ old('kategori', $produk->kategori) }}">

        </div>


        <div class="form-group">

            <label class="form-label">
                Deskripsi
            </label>

            <textarea
                class="form-input"
                name="deskripsi"
                rows="4">{{ old('deskripsi', $produk->deskripsi) }}</textarea>

        </div>


        <div class="form-group">

            <label class="form-label">
                Foto Produk
            </label>

            @if($produk->foto)

                <div style="margin-bottom:12px;">

                    <img
                        src="{{ asset('storage/' . $produk->foto) }}"
                        width="120"
                        style="
                            border-radius:12px;
                            object-fit:cover;
                        ">

                </div>

            @endif

            <input
                type="file"
                class="form-input"
                name="foto">

        </div>


        <hr style="margin:40px 0;">


        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        ">

            <h3 style="
                margin:0;
                font-size:18px;
                font-weight:700;
            ">
                Daftar Varian
            </h3>

        </div>


        @forelse($produk->varians as $varian)

            <div style="
                border:1px solid #e5e7eb;
                border-radius:16px;
                padding:18px;
                margin-bottom:18px;
                background:white;
            ">

                <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr 1fr 1fr;
                    gap:14px;
                ">

                    <div>

                        <label class="form-label">
                            Warna
                        </label>

                        <input
                            type="text"
                            class="form-input"
                            name="varians[{{ $varian->id }}][warna]"
                            value="{{ old('varians.' . $varian->id . '.warna', $varian->warna) }}">

                    </div>


                    <div>

                        <label class="form-label">
                            Size
                        </label>

                        <input
                            type="text"
                            class="form-input"
                            name="varians[{{ $varian->id }}][size]"
                            value="{{ old('varians.' . $varian->id . '.size', $varian->size) }}">

                    </div>


                    <div>

                        <label class="form-label">
                            Harga
                        </label>

                        <input
                            type="number"
                            class="form-input"
                            name="varians[{{ $varian->id }}][harga]"
                            value="{{ old('varians.' . $varian->id . '.harga', $varian->harga) }}"
                            required>

                    </div>


                    <div>

                        <label class="form-label">
                            Stok
                        </label>

                        <input
                            type="number"
                            class="form-input"
                            name="varians[{{ $varian->id }}][stok]"
                            value="{{ old('varians.' . $varian->id . '.stok', $varian->stok) }}"
                            required>

                    </div>

                </div>

            </div>

        @empty

            <div style="
                background:#fff7ed;
                color:#9a3412;
                padding:16px;
                border-radius:12px;
            ">
                Produk belum memiliki varian.
            </div>

        @endforelse


        <div style="
            margin-top:30px;
            display:flex;
            justify-content:flex-end;
        ">

            <button
                type="submit"
                class="form-btn">

                Simpan Semua Perubahan

            </button>

        </div>

    </form>

</div>

@endsection