<?php

namespace App\Models;

use App\Support\ReportStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ReportMatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'Match';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['matchCode', 'lostReportId', 'foundReportId', 'status', 'verifiedBy',
        'verifiedAt', 'revisionNote', 'createdAt', 'updatedAt'];

    public function lostReport() { return $this->belongsTo(LostReport::class, 'lostReportId'); }
    public function foundReport() { return $this->belongsTo(FoundReport::class, 'foundReportId'); }
    public function verifier() { return $this->belongsTo(User::class, 'verifiedBy'); }
    public function evidence() { return $this->hasOne(HandoverEvidence::class, 'matchId'); }

    public function getLabelStatusAttribute(): string { return ReportStatus::LABEL[$this->status] ?? $this->status; }
    public function getBadgeClassAttribute(): string { return ReportStatus::BADGE[$this->status] ?? ''; }

    /** Skor 0–100 (SC06): kategori 100, lokasi 100/85, waktu -8 per hari (min 60). */
    public function getSkorAttribute(): array
    {
        $l = $this->lostReport; $f = $this->foundReport;
        $selisih = (int) round(abs(Carbon::parse($l->lostDate)->diffInDays(Carbon::parse($f->foundDate))));
        $kategori = 100;
        $lokasi = strtolower(trim($l->floorRoom)) === strtolower(trim($f->floorRoom)) ? 100 : 85;
        $waktu = max(60, 100 - $selisih * 8);
        return ['kategori' => $kategori, 'lokasi' => $lokasi, 'waktu' => $waktu, 'selisih' => $selisih,
            'total' => (int) round(($kategori + $lokasi + $waktu) / 3)];
    }

    public function getUmurHariAttribute(): int
    {
        return (int) floor(Carbon::parse($this->createdAt)->diffInHours(now()) / 24);
    }

    public static function nextCode(): string
    {
        $max = (int) static::query()->selectRaw("MAX(CAST(SUBSTRING(matchCode, 3) AS UNSIGNED)) AS m")->value('m');
        return 'M-'.str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}