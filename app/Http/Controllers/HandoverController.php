<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [VP] SC05 — HandoverController
 * Pseudocode : PC013 requestDaftarTerverifikasi, PC014 getDetailSerahTerima,
 *              PC015 submitBukti, PC016 validasiKelengkapanBukti, PC017 selesaikanLaporan
 * Sequence   : SD-08 | Use Case: UC-08 | UI: UI-06 / UI-07
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh VP.
 *
 * PC013: daftar Match berstatus NOTIFIED ("Menunggu diambil") + riwayat CLOSED.
 * PC014: detail match + data pelapor (nama, NIM, kontak) untuk dicocokkan dengan KTM.
 * PC015/016: wajib 2 foto (pemilik memegang barang + KTM); simpan ke disk 'local'.
 * PC017: hanya bila bukti lengkap & KTM dicentang -> LostReport RETURNED lalu CLOSED,
 *        FoundReport CLOSED, Match CLOSED; catat petugas & waktu; kirim notifikasi.
 * foto(): sajikan bukti HANYA untuk Security atau pemilik barang ybs (privasi KTM).
 * tunda(): identitas KTM tidak sesuai -> tunda + notifikasi ke pelapor.
 */
class HandoverController extends Controller
{
    public function index()
    {
        abort(501, 'Belum dikerjakan: VP — PC013 daftar terverifikasi (UI-07).');
    }

    public function form(string $id)
    {
        abort(501, 'Belum dikerjakan: VP — PC014 detail serah terima.');
    }

    public function bukti(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — PC015/PC016 unggah & validasi 2 foto bukti.');
    }

    public function foto(Request $request, string $evidence, string $jenis)
    {
        abort(501, 'Belum dikerjakan: VP — penyajian bukti berotorisasi (privasi KTM).');
    }

    public function selesai(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — PC017 selesaikanLaporan() -> CLOSED.');
    }

    public function tunda(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — tunda serah terima (KTM tidak sesuai).');
    }
}