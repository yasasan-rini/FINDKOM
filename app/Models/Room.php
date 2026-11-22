<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** [VP] Entitas Room (ekstensi SCF-SRS-10/12) — STATUS: KERANGKA. */
class Room extends Model
{
    use HasUuids;
    protected $table = 'Room';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    // TODO VP:
    // protected $fillable = ['locationId', 'name'];
    // public function location() { return $this->belongsTo(Location::class, 'locationId'); }
    // public function getUsageAttribute(): int { jumlah laporan dgn locationId+floorRoom sama; }
    // public function getLabelAttribute(): string { "Gedung F — Lantai 2, Ruang F2.1"; }
}