<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [AR] SC03 — LoginController
 * Pseudocode : PC002 autentikasi(kredensial)
 * Sequence   : SD-02 | Use Case: UC-02 | UI: UI-02 / UI-002
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh AR.
 *
 * Yang harus dikerjakan (PC002):
 *  1. Validasi email (domain UB), password, peran (CIVITAS|SECURITY).
 *  2. Cari User berdasarkan email; Hash::check terhadap passwordHash.
 *  3. Cocokkan role akun dengan tab peran yang dipilih.
 *  4. Auth::login + session()->regenerate(); buat baris SesiPengguna
 *     (sid, userId, hakAkses, createdAt, expiredAt = now()+12 jam).
 *     Model SesiPengguna milik YN sudah siap dipakai.
 *  5. Redirect: SECURITY -> route('dashboard'), CIVITAS -> route('beranda').
 *  6. keluar(): hapus SesiPengguna, Auth::logout, invalidate + regenerateToken.
 *  7. demo(): login cepat akun demo (opsional, untuk presentasi).
 */
class LoginController extends Controller
{
    public function bukaHalamanLogin()
    {
        abort(501, 'Belum dikerjakan: AR — SC03 halaman login (UI-02).');
    }

    public function autentikasi(Request $request)
    {
        abort(501, 'Belum dikerjakan: AR — PC002 autentikasi().');
    }

    public function demo(Request $request, string $role)
    {
        abort(501, 'Belum dikerjakan: AR — login demo (opsional).');
    }

    public function keluar(Request $request)
    {
        abort(501, 'Belum dikerjakan: AR — keluar()/logout.');
    }
}