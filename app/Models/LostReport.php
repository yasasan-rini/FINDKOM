<?php

namespace App\Models;

use App\Models\Concerns\ReportFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * [BK] Entitas LostReport (Class Diagram Modul 2 §3.4)
 * STATUS: KERANGKA. Trait ReportFields sudah menyediakan relasi & accessor
 *         (location, category, reporter, matches, labelStatus, badgeClass, photoSrc).
 * TODO BK: deklarasikan $fillable sesuai DDL, lalu hapus $guarded.
 */
class LostReport extends Model
{
    use HasFactory, HasUuids, ReportFields;

    protected $table = 'LostReport';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    // Sementara: semua kolom boleh diisi kecuali PK.
    protected $guarded = ['id'];

    // TODO BK — ganti $guarded di atas dengan:
    // protected $fillable = ['reportCode','reporterId','locationId','floorRoom','lostDate',
    //     'lostTime','categoryId','description','photoUrl','status','createdAt'];
}