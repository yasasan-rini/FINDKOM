<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\ReportMatch;
use App\Models\User;
use App\Services\MatchingService;
use App\Support\ReportStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingLogicTest extends TestCase
{
    use RefreshDatabase;

    private function pasangan(string $ruangLost, string $ruangFound, int $selisihHari): ReportMatch
    {
        $loc = Location::factory()->create();
        $cat = Category::factory()->create();
        $user = User::factory()->create();

        $lost = LostReport::factory()->create([
            'reporterId' => $user->id, 'locationId' => $loc->id, 'categoryId' => $cat->id,
            'floorRoom' => $ruangLost, 'lostDate' => now()->subDays($selisihHari)->toDateString(),
            'status' => 'LOST_REPORTED',
        ]);
        $found = FoundReport::factory()->create([
            'reporterId' => $user->id, 'locationId' => $loc->id, 'categoryId' => $cat->id,
            'floorRoom' => $ruangFound, 'foundDate' => now()->toDateString(),
            'status' => 'FOUND_REPORTED',
        ]);

        return ReportMatch::create([
            'matchCode' => ReportMatch::nextCode(),
            'lostReportId' => $lost->id, 'foundReportId' => $found->id, 'status' => 'MATCH_CANDIDATE',
        ]);
    }

    /** PC007: ruang sama + tanggal sama = 100%. */
    public function test_skor_maksimal_bila_ruang_dan_tanggal_sama(): void
    {
        $s = $this->pasangan('F2.1', 'F2.1', 0)->skor;
        $this->assertSame(100, $s['kategori']);
        $this->assertSame(100, $s['lokasi']);
        $this->assertSame(100, $s['waktu']);
        $this->assertSame(100, $s['total']);
    }

    /** Ruang berbeda menurunkan skor lokasi ke 85; selisih 4 hari → waktu 68. */
    public function test_skor_turun_bila_ruang_berbeda_dan_selisih_empat_hari(): void
    {
        $s = $this->pasangan('F2.1', 'Koridor F2', 4)->skor;
        $this->assertSame(85, $s['lokasi']);
        $this->assertSame(68, $s['waktu']);
        $this->assertSame(4, $s['selisih']);
        $this->assertSame(84, $s['total']); // (100+85+68)/3 dibulatkan
    }

    /** Skor waktu tidak boleh di bawah 60 walaupun selisihnya jauh. */
    public function test_skor_waktu_memiliki_batas_bawah_enam_puluh(): void
    {
        $s = $this->pasangan('F2.1', 'F2.1', 10)->skor;
        $this->assertSame(60, $s['waktu']);
        $this->assertSame(87, $s['total']); // (100+100+60)/3 dibulatkan
    }

    /** UC-05: selisih 4 hari masih masuk jendela pencocokan. */
    public function test_pencocokan_menerima_selisih_empat_hari(): void
    {
        $m = $this->pasangan('F2.1', 'F2.1', 4);
        $lost = $m->lostReport; $found = $m->foundReport;
        $m->delete();
        $lost->update(['status' => 'LOST_REPORTED']);
        $found->update(['status' => 'FOUND_REPORTED']);

        $this->assertSame(1, MatchingService::run($found));
        $this->assertSame('MATCH_CANDIDATE', $lost->fresh()->status);
    }

    /** UC-05: selisih 5 hari di luar jendela → tidak ada kandidat. */
    public function test_pencocokan_menolak_selisih_lima_hari(): void
    {
        $m = $this->pasangan('F2.1', 'F2.1', 5);
        $lost = $m->lostReport; $found = $m->foundReport;
        $m->delete();
        $lost->update(['status' => 'LOST_REPORTED']);
        $found->update(['status' => 'FOUND_REPORTED']);

        $this->assertSame(0, MatchingService::run($found));
        $this->assertSame('LOST_REPORTED', $lost->fresh()->status);
    }

    /** IM03: pemetaan status ke langkah linimasa. */
    public function test_linimasa_memetakan_status_ke_langkah_yang_benar(): void
    {
        $this->assertSame(0, ReportStatus::INDEX['LOST_REPORTED']);
        $this->assertSame(1, ReportStatus::INDEX['MATCH_CANDIDATE']);
        $this->assertSame(2, ReportStatus::INDEX['VERIFIED_MATCH']);
        $this->assertSame(3, ReportStatus::INDEX['NOTIFIED']);
        $this->assertSame(4, ReportStatus::INDEX['CLOSED']);
        $this->assertCount(5, ReportStatus::STEPS);
        $this->assertSame('Menunggu Diambil', ReportStatus::LABEL['NOTIFIED']);
    }
}