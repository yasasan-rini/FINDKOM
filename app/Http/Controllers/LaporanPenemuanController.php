<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * [AR] SC04 — LaporanPenemuanController
 * Pseudocode : PC004 buatLaporan(data, foto)
 * Sequence   : SD-04 | Use Case: UC-04 | UI: UI-04 / UI-004
 * STATUS: BELUM DIKERJAKAN — silakan isi oleh AR.
 *
 * Yang harus dikerjakan (PC004):
 *  1. Validasi locationId, floorRoom, foundDate (<= hari ini), foundTime,
 *     categoryId, description (min 5), foto (WAJIB, image, maks 2 MB).
 *  2. DB::transaction: simpan foto, buat kode F-0001, reporterName dari user login,
 *     status FOUND_REPORTED, simpan FoundReport.
 *  3. Panggil \App\Services\MatchingService::run($laporan)  <-- milik YN, sudah siap.
 *  4. Tampilkan bukti kode + status + jumlah kandidat + instruksi titip ke pos Security.
 */
class LaporanPenemuanController extends Controller
{
    public function bukaFormPenemuan()
    {
        abort(501, 'Belum dikerjakan: AR — SC04 form penemuan (UI-04).');
    }

    public function buatLaporan(Request $request)
    {
        abort(501, 'Belum dikerjakan: AR — PC004 buatLaporan().');
    }
}