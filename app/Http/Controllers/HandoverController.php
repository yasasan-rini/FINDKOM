<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\HandoverEvidence;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HandoverController extends Controller
{
    // PC013 - requestDaftarTerverifikasi()
    public function index()
    {
        $daftarMatch = ReportMatch::with(['lostReport.category', 'lostReport.reporter', 'foundReport.location', 'evidence'])
            ->where('status', 'NOTIFIED')->orderBy('verifiedAt')->get();
        $riwayat = ReportMatch::with(['lostReport.category', 'lostReport.reporter', 'evidence.security'])
            ->where('status', 'CLOSED')->orderByDesc('updatedAt')->limit(10)->get();
        return view('handover.index', compact('daftarMatch', 'riwayat'));
    }

    // PC014 - getDetailSerahTerima(matchId)
    public function form(string $id)
    {
        $match = ReportMatch::with('evidence.security')->findOrFail($id);
        abort_unless(in_array($match->status, ['NOTIFIED', 'CLOSED']), 404);
        $lost = LostReport::with(['category', 'location'])->findOrFail($match->lostReportId);
        $found = FoundReport::with(['location'])->findOrFail($match->foundReportId);
        $pelapor = User::findOrFail($lost->reporterId);
        return view('handover.form', compact('match', 'lost', 'found', 'pelapor'));
    }

    // PC015 + PC016 - submitBukti / validasiKelengkapanBukti
    public function bukti(Request $request, string $id)
    {
        $request->validate([
            'fotoPemegangBarang' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'fotoKTM' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], ['*.required' => 'Bukti belum lengkap — unggah kedua foto.']);

        $match = ReportMatch::where('status', 'NOTIFIED')->findOrFail($id);
        $bukti = HandoverEvidence::firstOrNew(['matchId' => $match->id]);
        $lama = $bukti->exists ? [$bukti->photoHolding, $bukti->photoKtm] : [];

        $bukti->fill([
            'photoHolding' => $request->file('fotoPemegangBarang')->store('handover', 'public'),
            'photoKtm' => $request->file('fotoKTM')->store('handover', 'public'),
            'securityId' => auth()->id(), 'handoverAt' => now(),
        ])->save();

        foreach ($lama as $p) {
            if ($p && ! str_starts_with($p, 'img/')) Storage::disk('public')->delete($p);
        }

        return back()->with('success', 'Bukti serah terima tersimpan. Centang konfirmasi KTM untuk menutup laporan.');
    }

    // PC017 - selesaikanLaporan(matchId): RETURNED → CLOSED
    public function selesai(Request $request, string $id)
    {
        $request->validate(['ktm_confirmed' => 'accepted'],
            ['ktm_confirmed.accepted' => 'Konfirmasi kecocokan KTM wajib dicentang.']);
        if (! HandoverEvidence::where('matchId', $id)->exists()) {
            return back()->with('warning', 'Bukti belum lengkap');
        }
        DB::transaction(function () use ($id) {
            $match = ReportMatch::where('status', 'NOTIFIED')->findOrFail($id);
            $lost = LostReport::findOrFail($match->lostReportId);
            $lost->update(['status' => 'RETURNED']);
            $lost->update(['status' => 'CLOSED']);
            FoundReport::where('id', $match->foundReportId)->update(['status' => 'CLOSED']);
            $match->update(['status' => 'CLOSED']);
            Notification::create(['userId' => $lost->reporterId, 'matchId' => $match->id,
                'message' => "Laporan {$lost->reportCode} telah selesai. Barang telah diserahkan kembali. Terima kasih menggunakan FINDKOM!",
                'isRead' => false]);
        });
        return redirect()->route('handover.index')->with('success', 'Laporan telah selesai dan ditutup — bukti terarsip sebagai jejak audit.');
    }

    public function tunda(Request $request, string $id)
    {
        $request->validate(['alasan' => 'required|string|min:5|max:300']);
        $match = ReportMatch::findOrFail($id);
        $lost = LostReport::findOrFail($match->lostReportId);
        Notification::create(['userId' => $lost->reporterId, 'matchId' => $match->id,
            'message' => "Serah terima {$lost->reportCode} ditunda: ".$request->input('alasan').'. Hubungi Security untuk verifikasi ulang identitas.',
            'isRead' => false]);
        return redirect()->route('handover.index')->with('warning', 'Serah terima ditunda. Pelapor diminta verifikasi ulang identitas.');
    }
}