<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'Location';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name'];

    public function rooms() { return $this->hasMany(Room::class, 'locationId'); }
    public function lostReports() { return $this->hasMany(LostReport::class, 'locationId'); }
    public function foundReports() { return $this->hasMany(FoundReport::class, 'locationId'); }
    public function getUsageAttribute(): int { return $this->lostReports()->count() + $this->foundReports()->count(); }
}