<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoundReport extends Model
{
    protected $table = 'FoundReport';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'reportCode', 'reporterId', 'reporterName', 'locationId',
        'floorRoom', 'foundDate', 'foundTime', 'categoryId', 'description',
        'photoUrl', 'status', 'receivedBy', 'receivedAt', 'receiptNote',
        'createdAt', 'updatedAt',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationId');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporterId');
    }
}