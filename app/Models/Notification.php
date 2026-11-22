<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;
    protected $table = 'Notification';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['userId', 'matchId', 'message', 'isRead', 'createdAt'];
    protected $casts = ['isRead' => 'boolean'];

    public function user() { return $this->belongsTo(User::class, 'userId'); }
    public function match() { return $this->belongsTo(ReportMatch::class, 'matchId'); }
}