<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Services\MatchingService;
use App\Support\Pager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    // PC005 - requestKandidatSiapVerifikasi()
    public function index(Request $request)
    {
        $query = ReportMatch::with(['lostReport.location', 'lostReport.category', 'foundReport.location', 'foundReport.category'])
            ->where('status', 'MATCH_CANDIDATE');
        if ($request->query('tab') === 'baru') $query->where('createdAt', '>=', now()->subDay());
        if ($request->filled('locationId')) {
            $query->whereHas('lostReport', fn ($q) => $q->where('locationId', $request->query('locationId')));
        }
        if ($request->filled('tanggal')) {
            $tgl = $request->query('tanggal');
            $query->where(fn ($q) => $q
                ->whereHas('lostReport', fn ($l) => $l->whereDate('lostDate', $tgl))
                ->orWhereHas('foundReport', fn ($f) => $f->whereDate('foundDate', $tgl)));
        }
        $sort = $request->query('sort', 'old');
        $list = $query->get();
        $list = $sort === 'score' ? $list->sortByDesc(fn ($m) => $m->skor['total'])->values()
            : $list->sortBy('createdAt', SORT_REGULAR, $sort === 'new')->values();

        return view('match.index', [
            'daftarKandidat' => Pager::paginate($list, 12),
            'daftarGedung' => Location::orderBy('name')->get(),
        ]);
    }

    // PC006 - requestDetailKandidat(matchId)
    public function detail(string $id)
    {
        $match = ReportMatch::findOrFail($id);
        $lost = LostReport::with(['location', 'category', 'reporter'])->find($match->lostReportId);
        $found = FoundReport::with(['location', 'category', 'reporter', 'receiver'])->find($match->foundReportId);
        if (! $lost || ! $found) return redirect()->route('match.index')->with('error', 'Data laporan tidak ditemukan');
        return view('match.detail', compact('match', 'lost', 'found'));
    }

    // PC007 - verifikasiCocok(matchId, securityId)
    // Prasyarat UC-07: barang fisik sudah diterima di pos (UC-06).
    public function cocok(string $id)
    {
        $match = ReportMatch::where('status', 'MATCH_CANDIDATE')->findOrFail($id);
        $found = FoundReport::findOrFail($match->foundReportId);

        if (! $found->receivedAt) {
            return redirect()->route('receipt.index', ['id' => $found->id])
                ->with('warning', 'Barang fisik belum diterima di pos. Konfirmasi penerimaan (UC-06) sebelum verifikasi.');
        }

        DB::transaction(function () use ($match, $found) {
            $lost = LostReport::findOrFail($match->lostReportId);
            $match->update(['status' => 'VERIFIED_MATCH', 'verifiedBy' => auth()->id(), 'verifiedAt' => now()]);
            $lost->update(['status' => 'VERIFIED_MATCH']);
            $found->update(['status' => 'VERIFIED_MATCH']);
            Notification::create(['userId' => $lost->reporterId, 'matchId' => $match->id,
                'message' => 'Kemungkinan barangmu sudah ditemukan! Datang ke pos Security Gedung F dengan membawa KTM.',
                'isRead' => false]);
            $match->update(['status' => 'NOTIFIED']);
            $lost->update(['status' => 'NOTIFIED']);
            $found->update(['status' => 'NOTIFIED']);
        });

        return redirect()->route('match.index')->with('success', 'Verifikasi berhasil, notifikasi terkirim');
    }

    // PC008 - tolakKandidat(matchId)
    public function tolak(string $id)
    {
        DB::transaction(function () use ($id) {
            $match = ReportMatch::where('status', 'MATCH_CANDIDATE')->findOrFail($id);
            $match->update(['status' => 'REJECTED']);
            MatchingService::reopenIfNoCandidate($match->lostReportId, 'LOST');
            MatchingService::reopenIfNoCandidate($match->foundReportId, 'FOUND');
        });
        return redirect()->route('match.index')->with('warning', 'Kandidat ditolak — kedua laporan dibuka kembali untuk dicocokkan ulang.');
    }

    // PC009 - kirimHimbauanRevisi(matchId, catatan)
    public function revisi(Request $request, string $id)
    {
        $validated = $request->validate(['catatan' => 'required|string|min:5|max:500'],
            ['catatan.min' => 'Catatan minimal 5 karakter.']);
        $match = ReportMatch::findOrFail($id);
        $match->update(['revisionNote' => $validated['catatan']]);
        $lost = LostReport::findOrFail($match->lostReportId);
        Notification::create(['userId' => $lost->reporterId, 'matchId' => $match->id,
            'message' => 'Mohon lengkapi laporan '.$lost->reportCode.': '.$validated['catatan'], 'isRead' => false]);
        return back()->with('success', 'Pesan himbauan terkirim ke pelapor.');
    }
}