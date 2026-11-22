<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MatchController extends Controller
{
    // PC005: Menampilkan daftar kandidat siap verifikasi
    public function requestKandidatSiapVerifikasi()
    {
        $daftarKandidat = ReportMatch::with(['lostReport', 'foundReport'])
            ->where('status', 'MATCH_CANDIDATE')
            ->orderBy('createdAt', 'desc')
            ->get();

        if ($daftarKandidat->isEmpty()) {
            return view('match.index', ['daftarKandidat' => $daftarKandidat])
                ->with('info', 'Tidak ada laporan yang siap diverifikasi');
        }

        return view('match.index', compact('daftarKandidat'));
    }

    // PC006: Menampilkan detail kandidat
    public function requestDetailKandidat(string $matchId)
    {
        $match = ReportMatch::findOrFail($matchId);
        $lost = LostReport::with(['location', 'category'])->find($match->lostReportId);
        $found = FoundReport::with(['location', 'category'])->find($match->foundReportId);

        if (!$lost || !$found) {
            return redirect()->route('match.index')->with('error', 'Data laporan tidak ditemukan');
        }

        return view('match.detail', compact('match', 'lost', 'found'));
    }

    // PC007: Verifikasi cocok
    public function verifikasiCocok(string $matchId)
    {
        $securityId = auth()->id();

        DB::transaction(function () use ($matchId, $securityId) {
            $match = ReportMatch::findOrFail($matchId);
            $match->update([
                'status' => 'VERIFIED_MATCH',
                'verifiedBy' => $securityId,
                'verifiedAt' => now(),
            ]);

            $lost = LostReport::findOrFail($match->lostReportId);
            $found = FoundReport::findOrFail($match->foundReportId);

            $lost->update(['status' => 'VERIFIED_MATCH']);
            $found->update(['status' => 'VERIFIED_MATCH']);

            Notification::create([
                'id' => (string) Str::uuid(),
                'userId' => $lost->reporterId,
                'matchId' => $match->id,
                'message' => 'Barang Anda telah ditemukan dan siap diambil di pos Security.',
                'isRead' => false,
            ]);
        });

        return redirect()->route('match.index')->with('success', 'Verifikasi berhasil, notifikasi terkirim');
    }

    // PC008: Tolak kandidat
    public function tolakKandidat(string $matchId)
    {
        DB::transaction(function () use ($matchId) {
            $match = ReportMatch::findOrFail($matchId);
            $match->update(['status' => 'REJECTED']);

            LostReport::where('id', $match->lostReportId)->update(['status' => 'LOST_REPORTED']);
            FoundReport::where('id', $match->foundReportId)->update(['status' => 'FOUND_REPORTED']);
        });

        return redirect()->route('match.index')->with('info', 'Kandidat ditolak, sistem akan mencari kandidat lain');
    }

    // PC009: Kirim himbauan revisi
    public function kirimHimbauanRevisi(Request $request, string $matchId)
    {
        $validated = $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $match = ReportMatch::findOrFail($matchId);
        $match->update(['revisionNote' => $validated['catatan']]);

        $lost = LostReport::findOrFail($match->lostReportId);
        Notification::create([
            'id' => (string) Str::uuid(),
            'userId' => $lost->reporterId,
            'matchId' => $match->id,
            'message' => 'Mohon lengkapi laporan Anda: ' . $validated['catatan'],
            'isRead' => false,
        ]);

        return back()->with('success', 'Pesan terkirim');
    }
}