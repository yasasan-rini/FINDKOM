<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * [AR] Entitas User (Class Diagram Modul 2 §3.4)
 * STATUS: KERANGKA.
 * TODO AR: isi $fillable, getAuthPassword(), relasi, dan accessor initials.
 */
class User extends Authenticatable
{
    use HasFactory, HasUuids;

    protected $table = 'User';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $hidden = ['passwordHash'];

    // TODO AR — ganti $guarded dengan $fillable sesuai DDL:
    // ['namaLengkap','nim','email','username','passwordHash','kontak','role','createdAt']

    // TODO AR — WAJIB agar Auth::login bekerja dengan kolom passwordHash:
    // public function getAuthPassword() { return $this->passwordHash; }

    // TODO AR — helper & relasi:
    // public function isSecurity(): bool { return $this->role === 'SECURITY'; }
    // public function getInitialsAttribute(): string { ... }
    // public function lostReports()  { return $this->hasMany(LostReport::class, 'reporterId'); }
    // public function foundReports() { return $this->hasMany(FoundReport::class, 'reporterId'); }
    // public function notifications(){ return $this->hasMany(Notification::class, 'userId'); }
}