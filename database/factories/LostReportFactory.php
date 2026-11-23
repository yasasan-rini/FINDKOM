<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LostReportFactory extends Factory
{
    protected $model = LostReport::class;

    public function definition(): array
    {
        return [
            'reportCode' => LostReport::nextCode('L'),
            'reporterId' => User::factory(),
            'locationId' => Location::factory(),
            'floorRoom' => 'Lantai '.fake()->numberBetween(1, 4).', Ruang '.fake()->bothify('?#.##'),
            'lostDate' => now()->subDays(fake()->numberBetween(0, 5))->toDateString(),
            'lostTime' => fake()->time('H:i'),
            'categoryId' => Category::factory(),
            'description' => fake('id_ID')->sentence(10),
            'photoUrl' => null,
            'status' => 'LOST_REPORTED',
            'createdAt' => now(),
        ];
    }

    public function kandidat(): static { return $this->state(fn () => ['status' => 'MATCH_CANDIDATE']); }
    public function closed(): static { return $this->state(fn () => ['status' => 'CLOSED']); }
}