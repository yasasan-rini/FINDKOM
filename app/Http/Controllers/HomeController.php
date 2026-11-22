<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\HandoverEvidence;
use App\Models\LostReport;
use App\Models\ReportMatch;

class HomeController extends Controller
{
    /**
     * [BK] Beranda Civitas — BELUM DIKERJAKAN.
     * Kirim variabel: $aktif, $menunggu, $baruDitemukan ke view civitas.beranda.
     */
    public function beranda()
    {
        abort(501, 'Belum dikerjakan: BK — beranda civitas.');
    }

    /** [YN] IM02 · UI-11 Dashboard Security — SUDAH TERISI, jangan diubah anggota lain. */
    public function dashboard()
    {
        $stat = [
            'kandidat' => ReportMatch::where('status', 'MATCH_CANDIDATE')->count(),
            'hilang' => LostReport::where('status', '!=', 'CLOSED')->count(),
            'pos' => FoundReport::whereNotNull('receivedAt')->where('status', '!=', 'CLOSED')->count(),
            'selesai' => ReportMatch::where('status', 'CLOSED')->count(),
        ];
        $mendesak = ReportMatch::with(['lostReport.category', 'lostReport.location', 'foundReport'])
            ->where('status', 'MATCH_CANDIDATE')->orderBy('createdAt')->limit(5)->get();

        $chart = collect(range(6, 0))->map(function ($i) {
            $d = now()->subDays($i);
            return ['label' => $d->translatedFormat('D'), 'tgl' => $d->format('d/m'),
                'lost' => LostReport::whereDate('createdAt', $d->toDateString())->count(),
                'found' => FoundReport::whereDate('createdAt', $d->toDateString())->count()];
        });
        $chartMax = max(2, $chart->max('lost'), $chart->max('found'));

        $kategori = Category::withCount(['lostReports', 'foundReports'])->get()
            ->map(fn ($c) => ['nama' => $c->name, 'jumlah' => $c->lost_reports_count + $c->found_reports_count])
            ->sortByDesc('jumlah')->take(5)->values();

        $aktivitas = collect()
            ->concat(LostReport::latest('createdAt')->limit(4)->get()->map(fn ($r) =>
                ['teks' => "Laporan kehilangan {$r->reportCode} dibuat", 'waktu' => $r->createdAt, 'warna' => '#E4573D']))
            ->concat(FoundReport::latest('createdAt')->limit(4)->get()->map(fn ($r) =>
                ['teks' => "Laporan penemuan {$r->reportCode} dibuat", 'waktu' => $r->createdAt, 'warna' => '#2E9E5B']))
            ->concat(FoundReport::whereNotNull('receivedAt')->latest('receivedAt')->limit(3)->get()->map(fn ($r) =>
                ['teks' => "Barang {$r->reportCode} diterima di pos", 'waktu' => $r->receivedAt, 'warna' => '#7B5EA7']))
            ->concat(ReportMatch::whereNotNull('verifiedAt')->latest('verifiedAt')->limit(3)->get()->map(fn ($m) =>
                ['teks' => "Kecocokan {$m->matchCode} diverifikasi", 'waktu' => $m->verifiedAt, 'warna' => '#2D7DD2']))
            ->concat(HandoverEvidence::with('match')->latest('handoverAt')->limit(3)->get()->map(fn ($e) =>
                ['teks' => "Serah terima {$e->match?->matchCode} selesai — CLOSED", 'waktu' => $e->handoverAt, 'warna' => '#12676B']))
            ->sortByDesc('waktu')->take(9)->values();

        return view('security.dashboard', compact('stat', 'mendesak', 'chart', 'chartMax', 'kategori', 'aktivitas'));
    }
}