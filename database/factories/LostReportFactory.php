<?php

namespace Database\Factories;

use App\Models\LostReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** [BK] Factory LostReport — TODO BK: isi definition() sesuai DDL. */
class LostReportFactory extends Factory
{
    protected $model = LostReport::class;

    public function definition(): array
    {
        // TODO BK: reportCode, reporterId, locationId, floorRoom, lostDate, lostTime,
        //          categoryId, description, photoUrl, status, createdAt.
        return [];
    }
}