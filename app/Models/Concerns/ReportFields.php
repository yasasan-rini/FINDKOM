<?php

namespace App\Models\Concerns;

use App\Models\Category;
use App\Models\Location;
use App\Models\ReportMatch;
use App\Models\User;
use App\Support\ReportStatus;
use Illuminate\Support\Facades\Storage;

trait ReportFields
{
    public function location() { return $this->belongsTo(Location::class, 'locationId'); }
    public function category() { return $this->belongsTo(Category::class, 'categoryId'); }
    public function reporter() { return $this->belongsTo(User::class, 'reporterId'); }
    public function matches()
    {
        return $this->hasMany(ReportMatch::class, $this->tipe === 'LOST' ? 'lostReportId' : 'foundReportId');
    }

    public function getTipeAttribute(): string { return $this->getTable() === 'LostReport' ? 'LOST' : 'FOUND'; }
    public function getLabelStatusAttribute(): string { return ReportStatus::LABEL[$this->status] ?? $this->status; }
    public function getBadgeClassAttribute(): string { return ReportStatus::BADGE[$this->status] ?? ''; }
    public function getTimelineIndexAttribute(): int { return ReportStatus::INDEX[$this->status] ?? 0; }
    public function getEventDateAttribute(): string { return $this->tipe === 'LOST' ? $this->lostDate : $this->foundDate; }
    public function getEventTimeAttribute(): string { return substr($this->tipe === 'LOST' ? $this->lostTime : $this->foundTime, 0, 5); }
    public function getPhotoSrcAttribute(): string
    {
        if (! $this->photoUrl) return asset('img/placeholder.svg');
        return str_starts_with($this->photoUrl, 'img/') ? asset($this->photoUrl) : Storage::url($this->photoUrl);
    }
    public function getIsOpenAttribute(): bool
    {
        return $this->status === ($this->tipe === 'LOST' ? 'LOST_REPORTED' : 'FOUND_REPORTED');
    }
    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, [$this->tipe === 'LOST' ? 'LOST_REPORTED' : 'FOUND_REPORTED', 'MATCH_CANDIDATE'])
            && ! ($this->tipe === 'FOUND' && $this->receivedAt);
    }

    public static function nextCode(string $prefix): string
    {
        $max = (int) static::query()->selectRaw("MAX(CAST(SUBSTRING(reportCode, 3) AS UNSIGNED)) AS m")->value('m');
        return $prefix.'-'.str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}