<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPengguna extends Model
{
    protected $table = 'Session';
    protected $primaryKey = 'sid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['sid', 'userId', 'hakAkses', 'createdAt', 'expiredAt'];
}