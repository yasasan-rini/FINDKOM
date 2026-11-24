<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HandoverEvidence extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'HandoverEvidence';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['matchId', 'photoHolding', 'photoKtm', 'securityId', 'handoverAt', 'createdAt'];

    public function match() { return $this->belongsTo(ReportMatch::class, 'matchId'); }
    public function security() { return $this->belongsTo(User::class, 'securityId'); }

    public function getPhotoHoldingSrcAttribute(): string
    {
        return str_starts_with($this->photoHolding, 'img/') ? asset($this->photoHolding) : Storage::url($this->photoHolding);
    }
    public function getPhotoKtmSrcAttribute(): string
    {
        return str_starts_with($this->photoKtm, 'img/') ? asset($this->photoKtm) : Storage::url($this->photoKtm);
    }
}