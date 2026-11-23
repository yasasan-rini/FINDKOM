<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\Location;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaporanPenemuanController extends Controller
{
    public function bukaFormPenemuan()
    {
        return view('lapor.penemuan', [
            'laporan' => null,
            'daftarGedung' => Location::orderBy('name')->get(),
            'daftarKategori' => Category::orderBy('name')->get(),
        ]);
    }

    // PC004 - buatLaporan(data, foto)
    public function buatLaporan(Request $request)
    {
        $data = $request->validate([
            'locationId' => 'required|exists:Location,id',
            'floorRoom' => 'required|string|max:50',
            'foundDate' => 'required|date|before_or_equal:today',
            'foundTime' => 'required|date_format:H:i',
            'categoryId' => 'required|exists:Category,id',
            'description' => 'required|string|min:5|max:600',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'foto.required' => 'Foto barang temuan wajib diunggah.',
            'foto.image' => 'File harus berupa foto',
            'description.min' => 'Deskripsi minimal 5 karakter.',
            '*.required' => 'Data wajib dan foto barang harus diisi',
        ]);

        try {
            [$laporan, $jumlah] = DB::transaction(function () use ($data, $request) {
                $user = auth()->user();
                $laporan = FoundReport::create([
                    'reportCode' => FoundReport::nextCode('F'),
                    'reporterId' => $user->id, 'reporterName' => $user->namaLengkap,
                    'locationId' => $data['locationId'], 'floorRoom' => $data['floorRoom'],
                    'foundDate' => $data['foundDate'], 'foundTime' => $data['foundTime'],
                    'categoryId' => $data['categoryId'], 'description' => $data['description'],
                    'photoUrl' => $request->file('foto')->store('uploads/found', 'public'),
                    'status' => 'FOUND_REPORTED',
                ]);
                return [$laporan, MatchingService::run($laporan)];
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Laporan gagal dibuat. Silakan coba lagi.');
        }

        $pesan = 'Laporan kamu sudah terkirim.';
        $pesan .= $jumlah > 0 ? " Ditemukan {$jumlah} kandidat kecocokan!" : ' Jangan lupa titipkan barang ke pos Security.';
        return redirect()->route('penemuan.form')->with([
            'success' => $pesan, 'kodeLaporan' => $laporan->reportCode,
            'statusLaporan' => $laporan->status, 'jumlahKandidat' => $jumlah, 'tipeSukses' => 'FOUND',
        ]);
    }
}