<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostReport extends Model
{
    protected $table = 'LostReport';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'reportCode', 'reporterId', 'locationId', 'floorRoom',
        'lostDate', 'lostTime', 'categoryId', 'description', 'photoUrl',
        'status', 'createdAt',
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
