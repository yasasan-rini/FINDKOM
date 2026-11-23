<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'Category';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name'];

    public function lostReports() { return $this->hasMany(LostReport::class, 'categoryId'); }
    public function foundReports() { return $this->hasMany(FoundReport::class, 'categoryId'); }
    public function getUsageAttribute(): int { return $this->lostReports()->count() + $this->foundReports()->count(); }
}