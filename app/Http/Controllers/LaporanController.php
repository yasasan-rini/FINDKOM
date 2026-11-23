<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\HandoverEvidence;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Models\Room;
use App\Services\MatchingService;
use App\Support\Pager;
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

    /** UI-09 — Status & Riwayat + Notifikasi. */
    public function status(Request $request)
    {
        $uid = auth()->id();
        $filter = $request->query('filter', 'all');
        $lost = LostReport::with(['category', 'location', 'matches'])->where('reporterId', $uid);
        $found = FoundReport::with(['category', 'location', 'matches'])->where('reporterId', $uid);
        $semua = $this->gabung($lost, $found, 'new');
        $counts = ['all' => $semua->count(),
            'aktif' => $semua->whereNotIn('status', ['CLOSED', 'NOTIFIED'])->count(),
            'wait' => $semua->where('status', 'NOTIFIED')->count(),
            'done' => $semua->where('status', 'CLOSED')->count()];
        $laporan = match ($filter) {
            'aktif' => $semua->whereNotIn('status', ['CLOSED', 'NOTIFIED'])->values(),
            'wait' => $semua->where('status', 'NOTIFIED')->values(),
            'done' => $semua->where('status', 'CLOSED')->values(),
            default => $semua,
        };
        $notifikasi = Notification::where('userId', $uid)->orderByDesc('createdAt')->limit(10)->get();
        return view('laporan.status', compact('laporan', 'filter', 'counts', 'notifikasi'));
    }

    /** UI-10 — Cari Barang. */
    public function cari(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $terapkan = function ($query, string $dateCol) use ($request, $q) {
            $query->with(['category', 'location'])->where('status', '!=', 'CLOSED')
                ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
                    ->where('reportCode', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"))))
                ->when($request->filled('locationId'), fn ($w) => $w->where('locationId', $request->query('locationId')))
                ->when($request->filled('categoryId'), fn ($w) => $w->where('categoryId', $request->query('categoryId')))
                ->when($request->filled('tanggal'), fn ($w) => $w->whereDate($dateCol, $request->query('tanggal')))
                ->when($request->filled('range'), fn ($w) => $w->whereDate($dateCol, '>=', now()->subDays((int) $request->query('range'))->toDateString()));
            $this->filterRuangan($query, $request->query('room'));
        };
        $type = $request->query('type');
        $lost = LostReport::query(); $found = FoundReport::query();
        $terapkan($lost, 'lostDate'); $terapkan($found, 'foundDate');
        $items = $this->gabung($type === 'FOUND' ? $lost->whereRaw('1=0') : $lost,
            $type === 'LOST' ? $found->whereRaw('1=0') : $found, $request->query('sort', 'new'));

        return view('laporan.cari', [
            'hasil' => Pager::paginate($items, 12),
            'daftarGedung' => Location::orderBy('name')->get(),
            'daftarKategori' => Category::orderBy('name')->get(),
        ]);
    }

    /** Query bersama untuk UI-12 dan ekspor CSV — filter selalu identik. */
    private function querySemua(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $terapkan = function ($query) use ($request, $q) {
            $query->with(['category', 'location', 'reporter'])
                ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
                    ->where('reportCode', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('reporter', fn ($r) => $r->where('namaLengkap', 'like', "%{$q}%"))))
                ->when($request->filled('status'), fn ($w) => $w->where('status', $request->query('status')))
                ->when($request->filled('locationId'), fn ($w) => $w->where('locationId', $request->query('locationId')));
            $this->filterRuangan($query, $request->query('room'));
        };
        $type = $request->query('tipe');
        $lost = LostReport::query(); $found = FoundReport::query();
        $terapkan($lost); $terapkan($found);

        return $this->gabung($type === 'FOUND' ? $lost->whereRaw('1=0') : $lost,
            $type === 'LOST' ? $found->whereRaw('1=0') : $found, $request->query('sort', 'new'));
    }

    public function semua(Request $request)
    {
        return view('laporan.semua', [
            'laporan' => Pager::paginate($this->querySemua($request), 15),
            'daftarGedung' => Location::orderBy('name')->get(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $items = $this->querySemua($request);
        $nama = 'findkom-laporan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($items) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Kode', 'Jenis', 'Kategori', 'Gedung', 'Lantai/Ruang', 'Tanggal', 'Waktu', 'Status', 'Pelapor', 'Deskripsi'], ';');
            foreach ($items as $r) {
                fputcsv($out, [$r->reportCode, $r->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan',
                    $r->category->name, $r->location->name, $r->floorRoom, $r->eventDate, $r->eventTime,
                    $r->labelStatus, $r->reporter?->namaLengkap ?? '-', $r->description], ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

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