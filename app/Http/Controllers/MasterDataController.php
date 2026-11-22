<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [VP] MasterDataController — Master Gedung, Lantai/Ruangan, Kategori
 * Pseudocode : PC018 requestDaftar, PC019 tambahData, PC020 validasiData,
 *              PC021 hapusData, PC022 konfirmasiHapus
 * Sequence   : SD-09 | Use Case: UC-09 | UI: UI-08
 * SRS        : SCF-SRS-10, SCF-SRS-11, SCF-SRS-12 (semuanya Critical)
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh VP.
 *
 * index : tab jenis = gedung | ruangan | kategori (+ jumlah data & daftar gedung induk).
 * tambah: PC019 + PC020 (tolak nama duplikat; ruangan unik per gedung).
 * ubah  : rename dengan validasi duplikat yang sama.
 * hapus : PC021/PC022 — tolak bila data masih dipakai laporan.
 */
class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        abort(501, 'Belum dikerjakan: VP — PC018 daftar master data (UI-08).');
    }

    public function tambah(Request $request)
    {
        abort(501, 'Belum dikerjakan: VP — PC019/PC020 tambah & validasi data.');
    }

    public function ubah(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — ubah nama master data.');
    }

    public function hapus(Request $request, string $id)
    {
        abort(501, 'Belum dikerjakan: VP — PC021/PC022 hapus dengan konfirmasi.');
    }

    // TODO VP — PC020: private function validasiData(string $jenis, string $nama, ...): bool
}