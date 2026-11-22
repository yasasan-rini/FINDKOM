<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\HandoverEvidence;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\ReportMatch;
use App\Models\Room;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private function kelas(string $type): string
    {
        abort_unless(in_array($type, ['lost', 'found']), 404);
        return $type === 'lost' ? LostReport::class : FoundReport::class;
    }

    private function gabung($lostQuery, $foundQuery, string $sort)
    {
        $items = $lostQuery->get()->concat($foundQuery->get());
        return match ($sort) {
            'old' => $items->sortBy('createdAt')->values(),
            'event' => $items->sortByDesc('eventDate')->values(),
            'code' => $items->sortBy('reportCode')->values(),
            default => $items->sortByDesc('createdAt')->values(),
        };
    }

    private function filterRuangan($query, ?string $roomId)
    {
        if (! $roomId) return $query;
        $room = Room::find($roomId);
        return $room ? $query->where('locationId', $room->locationId)->where('floorRoom', $room->name) : $query;
    }

    /** [BK] UI-09 Status & Riwayat — BELUM DIKERJAKAN. */
    public function status(Request $request)
    {
        abort(501, 'Belum dikerjakan: BK — UI-09 status & riwayat.');
    }

    /** [AR] UI-10 Cari Barang — BELUM DIKERJAKAN. */
    public function cari(Request $request)
    {
        abort(501, 'Belum dikerjakan: AR — UI-10 cari barang.');
    }

    /** [VP] UI-12 Semua Laporan — BELUM DIKERJAKAN. */
    public function semua(Request $request)
    {
        abort(501, 'Belum dikerjakan: VP — UI-12 semua laporan.');
    }

    /** [VP] Ekspor CSV (mengikuti filter aktif) — BELUM DIKERJAKAN. */
    public function export(Request $request): StreamedResponse
    {
        abort(501, 'Belum dikerjakan: VP — ekspor CSV.');
    }

    /** [YN] Detail laporan (integrasi lintas modul) — SUDAH TERISI. */
    public function detail(string $type, string $code)
    {
        $laporan = $this->kelas($type)::with(['category', 'location', 'reporter'])->where('reportCode', $code)->firstOrFail();
        $isSec = auth()->user()->isSecurity();
        $own = $laporan->reporterId === auth()->id();
        abort_if($laporan->status === 'CLOSED' && ! $own && ! $isSec, 403);
        $related = ReportMatch::with(['lostReport.category', 'foundReport.category'])
            ->where($type === 'lost' ? 'lostReportId' : 'foundReportId', $laporan->id)->get();
        $evidence = HandoverEvidence::with('security')->whereIn('matchId', $related->pluck('id'))->first();
        return view('laporan.detail', compact('laporan', 'isSec', 'own', 'related', 'evidence'));
    }

    /** [YN] Ubah laporan milik sendiri — SUDAH TERISI (view form diisi BK/AR). */
    public function edit(string $type, string $id)
    {
        $laporan = $this->kelas($type)::findOrFail($id);
        abort_unless($laporan->reporterId === auth()->id() && $laporan->isEditable, 403);
        return view($type === 'lost' ? 'lapor.kehilangan' : 'lapor.penemuan', [
            'laporan' => $laporan,
            'daftarGedung' => Location::orderBy('name')->get(),
            'daftarKategori' => Category::orderBy('name')->get(),
        ]);
    }

    /** [YN] Simpan perubahan + jalankan ulang pencocokan — SUDAH TERISI. */
    public function update(Request $request, string $type, string $id)
    {
        $laporan = $this->kelas($type)::findOrFail($id);
        abort_unless($laporan->reporterId === auth()->id() && $laporan->isEditable, 403);
        $d = $type === 'lost' ? 'lostDate' : 'foundDate';
        $t = $type === 'lost' ? 'lostTime' : 'foundTime';
        $validated = $request->validate([
            'locationId' => 'required|exists:Location,id', 'floorRoom' => 'required|string|max:50',
            $d => 'required|date|before_or_equal:today', $t => 'required|date_format:H:i',
            'categoryId' => 'required|exists:Category,id', 'description' => 'required|string|min:5|max:600',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $file = $request->file('photo') ?? $request->file('foto');
        if ($file && $laporan->photoUrl && ! str_starts_with($laporan->photoUrl, 'img/')) {
            Storage::disk('public')->delete($laporan->photoUrl);
        }
        $laporan->update([
            'locationId' => $validated['locationId'], 'floorRoom' => $validated['floorRoom'],
            $d => $validated[$d], $t => $validated[$t], 'categoryId' => $validated['categoryId'],
            'description' => $validated['description'],
            'photoUrl' => $file ? $file->store('uploads/'.$type, 'public') : $laporan->photoUrl,
        ]);
        $jumlah = $laporan->isOpen ? MatchingService::run($laporan) : 0;
        return redirect()->route('laporan.status')->with('success',
            "Laporan {$laporan->reportCode} diperbarui.".($jumlah ? " {$jumlah} kandidat baru ditemukan!" : ''));
    }

    /** [YN] Hapus laporan milik sendiri + buka kembali kandidat — SUDAH TERISI. */
    public function destroy(string $type, string $id)
    {
        $laporan = $this->kelas($type)::findOrFail($id);
        abort_unless($laporan->reporterId === auth()->id() && $laporan->isEditable, 403);
        DB::transaction(function () use ($laporan, $type) {
            $col = $type === 'lost' ? 'lostReportId' : 'foundReportId';
            foreach (ReportMatch::where($col, $laporan->id)->where('status', 'MATCH_CANDIDATE')->get() as $m) {
                $m->delete();
                MatchingService::reopenIfNoCandidate($type === 'lost' ? $m->foundReportId : $m->lostReportId, $type === 'lost' ? 'FOUND' : 'LOST');
            }
            if ($laporan->photoUrl && ! str_starts_with($laporan->photoUrl, 'img/')) Storage::disk('public')->delete($laporan->photoUrl);
            $laporan->delete();
        });
        return redirect()->route('laporan.status')->with('warning', "Laporan {$laporan->reportCode} dihapus.");
    }
}