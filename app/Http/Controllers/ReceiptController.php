<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [VP] ReceiptController — Konfirmasi Penerimaan Barang Fisik
 * Pseudocode : PC010 requestHalaman, PC011 cariLaporanPenemuan, PC012 konfirmasiPenerimaan
 * Sequence   : SD-06 | Use Case: UC-06 | UI: UI-06
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh VP.
 *
 * PC010/011: tampilkan form pencarian + cari FoundReport berdasarkan kode/nama pelapor
 *            (hanya yang belum diterima & belum CLOSED); tampilkan detail + foto.
 * PC012    : catat receivedBy = auth()->id(), receivedAt = now(), receiptNote (opsional),
 *            lalu kirim notifikasi ke pelapor penemuan.
 */
class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        abort(501, 'Belum dikerjakan: VP — PC010/PC011 halaman & pencarian penerimaan (UI-06).');
    }

    public function konfirmasi(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — PC012 konfirmasiPenerimaan().');
    }
}