<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportMatch extends Model
{
    protected $table = 'Match';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'lostReportId', 'foundReportId', 'status',
        'verifiedBy', 'verifiedAt', 'revisionNote', 'createdAt', 'updatedAt',
    ];

    public function lostReport()
    {
        return $this->belongsTo(LostReport::class, 'lostReportId');
    }

    public function foundReport()
    {
        return $this->belongsTo(FoundReport::class, 'foundReportId');
    }

    public function security()
    {
        return $this->belongsTo(User::class, 'verifiedBy');
    }
}