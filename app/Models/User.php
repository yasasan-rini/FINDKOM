<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'User';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'namaLengkap', 'nim', 'email', 'username',
        'passwordHash', 'kontak', 'role', 'createdAt',
    ];

    public function getAuthPassword()
    {
        return $this->passwordHash;
    }
}