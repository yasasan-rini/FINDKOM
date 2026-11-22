<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [BK] SC02 — LaporanKehilanganController
 * Pseudocode : PC003 buatLaporan(data: LaporanKehilanganDTO)
 * Sequence   : SD-03 | Use Case: UC-03 | UI: UI-03 / UI-003
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh BK.
 *
 * Yang harus dikerjakan (PC003):
 *  1. Validasi locationId, floorRoom, lostDate (<= hari ini), lostTime,
 *     categoryId, description (min 5), photo (opsional, image, maks 2 MB).
 *  2. DB::transaction: simpan foto bila ada, buat kode laporan L-0001,
 *     status LOST_REPORTED, simpan LostReport.
 *  3. Panggil \App\Services\MatchingService::run($laporan)  <-- milik YN, sudah siap.
 *  4. Tampilkan bukti: kode laporan + status + jumlah kandidat.
 *  5. Gagal simpan -> pesan "Laporan gagal dibuat".
 */
class LaporanKehilanganController extends Controller
{
    public function form()
    {
        abort(501, 'Belum dikerjakan: BK — SC02 form laporan kehilangan (UI-03).');
    }

    public function buatLaporan(Request $request)
    {
        abort(501, 'Belum dikerjakan: BK — PC003 buatLaporan().');
    }
}