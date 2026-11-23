<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasUuids;
    protected $table = 'Room';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['locationId', 'name'];

    public function location() { return $this->belongsTo(Location::class, 'locationId'); }

    /** Dipakai laporan? (dicocokkan pada pasangan gedung + floorRoom) */
    public function getUsageAttribute(): int
    {
        return LostReport::where('locationId', $this->locationId)->where('floorRoom', $this->name)->count()
             + FoundReport::where('locationId', $this->locationId)->where('floorRoom', $this->name)->count();
    }

    public function getLabelAttribute(): string
    {
        return ($this->location?->name ?? '—').' — '.$this->name;
    }
}