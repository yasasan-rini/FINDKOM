<?php

namespace App\Models;

use App\Models\Concerns\ReportFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundReport extends Model
{
    use HasFactory, HasUuids, ReportFields;

    protected $table = 'FoundReport';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['reportCode', 'reporterId', 'reporterName', 'locationId', 'floorRoom', 'foundDate',
        'foundTime', 'categoryId', 'description', 'photoUrl', 'status', 'receivedBy', 'receivedAt',
        'receiptNote', 'createdAt', 'updatedAt'];

    public function receiver() { return $this->belongsTo(User::class, 'receivedBy'); }
}