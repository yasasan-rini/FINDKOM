<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasUuids;

    protected $table = 'User';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['namaLengkap', 'nim', 'email', 'username', 'passwordHash', 'kontak', 'role', 'createdAt'];
    protected $hidden = ['passwordHash'];

    public function getAuthPassword() { return $this->passwordHash; }
    public function isSecurity(): bool { return $this->role === 'SECURITY'; }
    public function getInitialsAttribute(): string
    {
        return strtoupper(collect(explode(' ', $this->namaLengkap))->take(2)->map(fn ($w) => $w[0] ?? '')->join(''));
    }

    public function lostReports() { return $this->hasMany(LostReport::class, 'reporterId'); }
    public function foundReports() { return $this->hasMany(FoundReport::class, 'reporterId'); }
    public function notifications() { return $this->hasMany(Notification::class, 'userId'); }
}