<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    // ── GET /promo — semua promo (pemilik: semua, karyawan: milik sendiri) ───────
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Promo::with(['pengaju:id,nama', 'penyetuju:id,nama'])->latest();

        if ($user->isKaryawan()) {
            $query->where('diajukan_oleh', $user->id);
        }

        $promos = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $promos,
        ]);
    }

    // ── POST /promo — karyawan ajukan promo baru ─────────────────────────────────
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isKaryawan()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya karyawan yang dapat mengajukan promo',
            ], 403);
        }

        $validated = $request->validate([
            'nama_promo'      => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'diskon'          => 'required|numeric|min:0',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'min_pembelian'   => 'nullable|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('promos', 'public');
        }

        $promo = Promo::create([
            'diajukan_oleh'  => $user->id,
            'nama_promo'     => $validated['nama_promo'],
            'deskripsi'      => $validated['deskripsi'] ?? null,
            'diskon'         => $validated['diskon'],
            'tipe_diskon'    => $validated['tipe_diskon'],
            'min_pembelian'  => $validated['min_pembelian'] ?? 0,
            'tanggal_mulai'  => $validated['tanggal_mulai'],
            'tanggal_selesai'=> $validated['tanggal_selesai'],
            'status'         => 'pending',
            'foto'           => $fotoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diajukan, menunggu persetujuan pemilik',
            'data'    => $promo->load('pengaju:id,nama'),
        ], 201);
    }

    // ── PUT /promo/{id}/approve — pemilik setujui promo ──────────────────────────
    public function approve(Request $request, $id)
    {
        $user = $request->user();

        if (! $user->isPemilik()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pemilik yang dapat menyetujui promo',
            ], 403);
        }

        $promo = Promo::findOrFail($id);

        if ($promo->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Promo ini sudah diproses sebelumnya (status: ' . $promo->status . ')',
            ], 422);
        }

        $promo->update([
            'status'          => 'aktif',
            'disetujui_oleh'  => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo disetujui dan sekarang aktif',
            'data'    => $promo->load(['pengaju:id,nama', 'penyetuju:id,nama']),
        ]);
    }

    // ── PUT /promo/{id}/reject — pemilik tolak promo ─────────────────────────────
    public function reject(Request $request, $id)
    {
        $user = $request->user();

        if (! $user->isPemilik()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pemilik yang dapat menolak promo',
            ], 403);
        }

        $promo = Promo::findOrFail($id);

        if ($promo->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Promo ini sudah diproses sebelumnya (status: ' . $promo->status . ')',
            ], 422);
        }

        $promo->update([
            'status'         => 'ditolak',
            'disetujui_oleh' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo ditolak',
            'data'    => $promo->load(['pengaju:id,nama', 'penyetuju:id,nama']),
        ]);
    }
}