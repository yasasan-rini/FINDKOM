<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoverEvidence extends Model
{
    protected $table = 'HandoverEvidence';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'matchId', 'photoHolding', 'photoKtm',
        'securityId', 'handoverAt', 'createdAt',
    ];

    public function match()
    {
        return $this->belongsTo(ReportMatch::class, 'matchId');
    }

    public function security()
    {
        return $this->belongsTo(User::class, 'securityId');
    }
}