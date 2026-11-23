<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\LostReport;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaporanKehilanganController extends Controller
{
    public function form()
    {
        return view('lapor.kehilangan', [
            'laporan' => null,
            'daftarGedung' => Location::orderBy('name')->get(),
            'daftarKategori' => Category::orderBy('name')->get(),
        ]);
    }

    // PC003 - buatLaporan(data: LaporanKehilanganDTO)
    public function buatLaporan(Request $request)
    {
        $validated = $request->validate([
            'locationId' => 'required|exists:Location,id',
            'floorRoom' => 'required|string|max:50',
            'lostDate' => 'required|date|before_or_equal:today',
            'lostTime' => 'required|date_format:H:i',
            'categoryId' => 'required|exists:Category,id',
            'description' => 'required|string|min:5|max:600',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], ['description.min' => 'Deskripsi minimal 5 karakter.', 'photo.max' => 'Ukuran foto maksimal 2 MB.']);

        try {
            [$laporan, $jumlah] = DB::transaction(function () use ($validated, $request) {
                $photoUrl = $request->hasFile('photo') ? $request->file('photo')->store('uploads/lost', 'public') : null;
                $laporan = LostReport::create([
                    'reportCode' => LostReport::nextCode('L'),
                    'reporterId' => auth()->id(),
                    'locationId' => $validated['locationId'], 'floorRoom' => $validated['floorRoom'],
                    'lostDate' => $validated['lostDate'], 'lostTime' => $validated['lostTime'],
                    'categoryId' => $validated['categoryId'], 'description' => $validated['description'],
                    'photoUrl' => $photoUrl, 'status' => 'LOST_REPORTED',
                ]);
                return [$laporan, MatchingService::run($laporan)];
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Laporan gagal dibuat. Silakan coba lagi.');
        }

        $pesan = 'Laporan kamu sudah terkirim.';
        $pesan .= $jumlah > 0 ? " Ditemukan {$jumlah} kandidat kecocokan!" : ' Kami mencocokkan otomatis saat ada laporan penemuan baru.';
        return redirect()->route('kehilangan.form')->with([
            'success' => $pesan, 'kodeLaporan' => $laporan->reportCode,
            'statusLaporan' => $laporan->status, 'jumlahKandidat' => $jumlah, 'tipeSukses' => 'LOST',
        ]);
    }
}