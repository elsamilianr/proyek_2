@extends('layouts.karyawan')

@section('title', 'Pembayaran Midtrans')

@push('styles')
<style>
.midtrans-card {
    max-width: 480px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    border: 1px solid var(--pink-border);
    padding: 28px 28px 24px;
    font-family: 'Nunito', sans-serif;
}
.midtrans-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #FFF7ED;
    border: 1px solid #FED7AA;
    color: #92400E;
    font-size: 12px;
    font-weight: 700;
    border-radius: 99px;
    padding: 4px 12px;
    margin-bottom: 16px;
}
.midtrans-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 6px;
}
.midtrans-total {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-dark);
}
.btn-pay {
    width: 100%;
    padding: 16px;
    background: var(--pink-btn);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    font-family: 'Nunito', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    transition: opacity .2s;
}
.btn-pay:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 13px;
    font-weight: 700;
}
.status-pending  { background:#FEF3C7; color:#D97706; }
.status-lunas    { background:#D1FAE5; color:#059669; }
.status-batal    { background:#FEE2E2; color:#DC2626; }
.spinner {
    width: 20px; height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin .8s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }
</style>
@endpush

@section('content')

<div class="page-header no-print">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('karyawan.transaksi.create') }}"
           style="display:flex;align-items:center;gap:4px;color:var(--text-gray);text-decoration:none;font-weight:600;font-size:14px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h1 class="page-title">Pembayaran Midtrans</h1>
    </div>
</div>

<div class="midtrans-card">

    {{-- Badge status --}}
    <div>
        <span class="midtrans-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            Midtrans E-Payment
        </span>
    </div>

    {{-- Info transaksi --}}
    <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px dashed var(--pink-border);">
        <div class="midtrans-info-row">
            <span style="color:var(--text-gray);">Kode Transaksi</span>
            <span style="font-weight:700;">{{ $transaksi->kode_transaksi }}</span>
        </div>
        <div class="midtrans-info-row">
            <span style="color:var(--text-gray);">Kasir</span>
            <span>{{ $transaksi->karyawan->nama ?? $transaksi->karyawan->name }}</span>
        </div>
        <div class="midtrans-info-row">
            <span style="color:var(--text-gray);">Status</span>
            <span id="statusBadge"
                  class="status-badge {{ $transaksi->status_bayar === 'lunas' ? 'status-lunas' : ($transaksi->status_bayar === 'batal' ? 'status-batal' : 'status-pending') }}">
                {{ $transaksi->label_status_bayar }}
            </span>
        </div>
    </div>

    {{-- Item detail --}}
    <div style="margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed var(--pink-border);">
        @foreach($transaksi->details as $detail)
        <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;">
            <div>
                <div style="font-weight:700;">{{ $detail->varian->produk->nama_produk ?? 'Produk' }}</div>
                <div style="color:var(--text-gray);font-size:12px;">
                    {{ $detail->varian->label ?? 'Default' }} × {{ $detail->jumlah }}
                </div>
            </div>
            <div style="font-weight:700;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</div>
        </div>
        @endforeach
    </div>

    {{-- Total --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
        <span style="font-size:14px;color:var(--text-gray);">Subtotal</span>
        <span>Rp{{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
    </div>
    @if($transaksi->diskon > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;font-size:13px;">
        <span style="color:var(--text-gray);">Diskon</span>
        <span style="color:#EF4444;">-Rp{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
    </div>
    @endif
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:2px solid var(--text-dark);">
        <span style="font-size:15px;font-weight:800;">TOTAL</span>
        <span class="midtrans-total">Rp{{ number_format($transaksi->total, 0, ',', '.') }}</span>
    </div>

    {{-- Tombol bayar / lunas --}}
    @if($transaksi->status_bayar === 'lunas')
        <div style="text-align:center;margin-top:20px;padding:16px;background:#D1FAE5;border-radius:14px;">
            <div style="font-size:24px;">✅</div>
            <div style="font-weight:700;color:#059669;margin-top:4px;">Pembayaran Lunas!</div>
        </div>
        <a href="{{ route('karyawan.transaksi.struk', $transaksi->id) }}"
           style="display:block;text-align:center;margin-top:12px;padding:14px;background:var(--pink-btn);color:white;border-radius:14px;font-weight:700;text-decoration:none;">
            Lihat Struk
        </a>
    @elseif($transaksi->status_bayar === 'batal')
        <div style="text-align:center;margin-top:20px;padding:16px;background:#FEE2E2;border-radius:14px;">
            <div style="font-size:24px;">❌</div>
            <div style="font-weight:700;color:#DC2626;margin-top:4px;">Pembayaran Dibatalkan</div>
        </div>
        <a href="{{ route('karyawan.transaksi.create') }}"
           style="display:block;text-align:center;margin-top:12px;padding:14px;background:var(--pink-btn);color:white;border-radius:14px;font-weight:700;text-decoration:none;">
            Buat Transaksi Baru
        </a>
    @else
        {{-- Masih pending - tampilkan tombol bayar --}}
        <button id="btnBayar" class="btn-pay" onclick="bukaSnapPopup()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            Bayar Sekarang
        </button>
        <div style="text-align:center;font-size:12px;color:var(--text-gray);margin-top:10px;">
            GoPay · OVO · DANA · QRIS · Transfer Bank · Kartu Kredit & lainnya
        </div>
    @endif

</div>

@endsection

@push('scripts')
{{-- Midtrans Snap.js --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
const TRANSAKSI_ID = {{ $transaksi->id }};
const TOKEN_URL    = "{{ route('karyawan.transaksi.midtrans.token', $transaksi->id) }}";
const KONFIRMASI_URL = "{{ route('karyawan.transaksi.midtrans.konfirmasi', $transaksi->id) }}";

async function bukaSnapPopup() {
    const btn = document.getElementById('btnBayar');
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner"></div> Memuat...';

    try {
        // 1. Ambil snap token dari server
        const res = await fetch(TOKEN_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        const data = await res.json();

        if (data.error) {
            alert('Gagal memuat pembayaran: ' + data.error);
            resetBtn();
            return;
        }

        // 2. Buka Snap popup
        window.snap.pay(data.snap_token, {
            onSuccess: function(result) {
                konfirmasiKeSever('settlement');
            },
            onPending: function(result) {
                konfirmasiKeSever('pending');
            },
            onError: function(result) {
                konfirmasiKeSever('deny');
            },
            onClose: function() {
                // User menutup popup tanpa bayar
                resetBtn();
            }
        });

    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan. Coba lagi.');
        resetBtn();
    }
}

async function konfirmasiKeSever(resultCode) {
    try {
        const res = await fetch(KONFIRMASI_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ result_code: resultCode }),
        });
        const data = await res.json();

        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.status === 'pending') {
            // Tampilkan pesan pending
            const badge = document.getElementById('statusBadge');
            if (badge) {
                badge.className = 'status-badge status-pending';
                badge.textContent = 'Menunggu Pembayaran';
            }
            resetBtn();
        } else {
            resetBtn();
        }
    } catch(err) {
        console.error(err);
        resetBtn();
    }
}

function resetBtn() {
    const btn = document.getElementById('btnBayar');
    if (!btn) return;
    btn.disabled = false;
    btn.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
        </svg>
        Bayar Sekarang`;
}
</script>
@endpush