<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'Notification';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'userId', 'matchId', 'message', 'isRead', 'createdAt'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function match()
    {
        return $this->belongsTo(ReportMatch::class, 'matchId');
    }
}