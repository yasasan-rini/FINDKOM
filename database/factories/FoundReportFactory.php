<?php

namespace Database\Factories;

use App\Models\FoundReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** [AR] Factory FoundReport — TODO AR: isi definition() sesuai DDL (photoUrl wajib). */
class FoundReportFactory extends Factory
{
    protected $model = FoundReport::class;

    public function definition(): array
    {
        // TODO AR: reportCode, reporterId, reporterName, locationId, floorRoom,
        //          foundDate, foundTime, categoryId, description, photoUrl,
        //          status FOUND_REPORTED, createdAt.
        return [];
    }
}