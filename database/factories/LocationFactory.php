<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/** [VP] Factory Location — TODO VP: isi definition() (name unik). */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        // TODO VP: return ['name' => 'Gedung ' . huruf/angka unik];
        return [];
    }
}