<?php

namespace App\Models;

use App\Models\Concerns\ReportFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostReport extends Model
{
    use HasFactory, HasUuids, ReportFields;

    protected $table = 'LostReport';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['reportCode', 'reporterId', 'locationId', 'floorRoom', 'lostDate', 'lostTime',
        'categoryId', 'description', 'photoUrl', 'status', 'createdAt'];
}