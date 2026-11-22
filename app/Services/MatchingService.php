<?php

namespace App\Services;

use App\Models\FoundReport;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Models\User;
use Illuminate\Support\Carbon;

class MatchingService
{
    /**
     * Pencocokan otomatis (UC-05 / SC02 / SC04):
     * lawan jenis, status terbuka, gedung & kategori sama, selisih tanggal ≤ 4 hari.
     */
    public static function run(LostReport|FoundReport $report): int
    {
        $isLost = $report instanceof LostReport;
        $oppClass = $isLost ? FoundReport::class : LostReport::class;
        $oppOpen = $isLost ? 'FOUND_REPORTED' : 'LOST_REPORTED';
        $dateCol = $isLost ? 'foundDate' : 'lostDate';
        $tgl = Carbon::parse($report->eventDate);

        $kandidat = $oppClass::where('status', $oppOpen)
            ->where('locationId', $report->locationId)
            ->where('categoryId', $report->categoryId)
            ->whereBetween($dateCol, [$tgl->copy()->subDays(4)->toDateString(), $tgl->copy()->addDays(4)->toDateString()])
            ->get();

        $dibuat = 0;
        foreach ($kandidat as $opp) {
            $lostId = $isLost ? $report->id : $opp->id;
            $foundId = $isLost ? $opp->id : $report->id;
            if (ReportMatch::where('lostReportId', $lostId)->where('foundReportId', $foundId)
                ->where('status', '!=', 'REJECTED')->exists()) continue;

            $m = ReportMatch::create([
                'matchCode' => ReportMatch::nextCode(),
                'lostReportId' => $lostId, 'foundReportId' => $foundId, 'status' => 'MATCH_CANDIDATE',
            ]);
            $opp->update(['status' => 'MATCH_CANDIDATE']);
            $dibuat++;

            foreach (User::where('role', 'SECURITY')->get() as $sec) {
                Notification::create(['userId' => $sec->id, 'matchId' => $m->id,
                    'message' => "Kandidat baru {$m->matchCode} menunggu verifikasi — {$report->category->name} di {$report->location->name}.",
                    'isRead' => false]);
            }
        }

        if ($dibuat > 0) {
            $report->update(['status' => 'MATCH_CANDIDATE']);
            Notification::create(['userId' => $report->reporterId, 'matchId' => null,
                'message' => "Sistem menemukan {$dibuat} kandidat kecocokan untuk laporan {$report->reportCode}. Security sedang meninjau.",
                'isRead' => false]);
        }
        return $dibuat;
    }

    /** Buka kembali laporan bila tidak punya kandidat lain (PC008). */
    public static function reopenIfNoCandidate(string $reportId, string $type): void
    {
        $col = $type === 'LOST' ? 'lostReportId' : 'foundReportId';
        $masih = ReportMatch::where($col, $reportId)->where('status', 'MATCH_CANDIDATE')->exists();
        if ($masih) return;
        $class = $type === 'LOST' ? LostReport::class : FoundReport::class;
        $r = $class::find($reportId);
        if ($r && ! in_array($r->status, ['NOTIFIED', 'CLOSED'])) {
            $r->update(['status' => $type === 'LOST' ? 'LOST_REPORTED' : 'FOUND_REPORTED']);
        }
    }
}