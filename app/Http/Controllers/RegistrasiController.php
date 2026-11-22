<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [BK] SC01 — RegistrasiController
 * Pseudocode : PC001 registrasi(data: RegistrasiDTO)
 * Sequence   : SD-01 | Use Case: UC-01 | UI: UI-01 / UI-001
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh BK.
 *
 * Yang harus dikerjakan (PC001):
 *  1. Validasi namaLengkap, nim, email (domain UB), username, password (min 8), kontak.
 *  2. Cek duplikat email / username / nim  -> tolak "Akun sudah terdaftar".
 *  3. Hash::make(password) -> kolom passwordHash; role default CIVITAS.
 *  4. Simpan User, redirect ke route('login') dengan pesan berhasil.
 */
class RegistrasiController extends Controller
{
    public function form()
    {
        abort(501, 'Belum dikerjakan: BK — SC01 form registrasi (UI-01).');
    }

    public function registrasi(Request $request)
    {
        abort(501, 'Belum dikerjakan: BK — PC001 registrasi().');
    }
}