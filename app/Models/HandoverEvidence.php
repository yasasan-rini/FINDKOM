<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * [VP] Entitas HandoverEvidence — STATUS: KERANGKA.
 * PENTING (privasi): foto KTM adalah dokumen identitas. Simpan di disk 'local'
 * (storage/app/private) dan sajikan lewat route berotorisasi, BUKAN /storage publik.
 */
class HandoverEvidence extends Model
{
    use HasUuids;
    protected $table = 'HandoverEvidence';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    // TODO VP:
    // protected $fillable = ['matchId','photoHolding','photoKtm','securityId','handoverAt','createdAt'];
    // public function match()    { return $this->belongsTo(ReportMatch::class, 'matchId'); }
    // public function security() { return $this->belongsTo(User::class, 'securityId'); }
    // public function getPhotoHoldingSrcAttribute(): string { ... }
    // public function getPhotoKtmSrcAttribute(): string { ... }
    //
    // Route bukti sudah disiapkan YN: GET /serah-terima/bukti/{evidence}/{jenis}
    // (nama route: handover.foto). Implementasi method foto() ada di HandoverController.
}