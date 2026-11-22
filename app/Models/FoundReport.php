<?php

namespace App\Models;

use App\Models\Concerns\ReportFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * [AR] Entitas FoundReport
 * STATUS: KERANGKA. Trait ReportFields sudah menyediakan relasi & accessor umum.
 * TODO AR: isi $fillable dan relasi receiver().
 */
class FoundReport extends Model
{
    use HasFactory, HasUuids, ReportFields;

    protected $table = 'FoundReport';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    // TODO AR — ganti $guarded dengan $fillable sesuai DDL, lalu tambah:
    // public function receiver() { return $this->belongsTo(User::class, 'receivedBy'); }
}