<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoundReportFactory extends Factory
{
    protected $model = FoundReport::class;

    public function definition(): array
    {
        return [
            'reportCode' => FoundReport::nextCode('F'),
            'reporterId' => User::factory(),
            'reporterName' => fake('id_ID')->name(),
            'locationId' => Location::factory(),
            'floorRoom' => 'Lantai '.fake()->numberBetween(1, 4).', Koridor '.fake()->bothify('?#'),
            'foundDate' => now()->subDays(fake()->numberBetween(0, 5))->toDateString(),
            'foundTime' => fake()->time('H:i'),
            'categoryId' => Category::factory(),
            'description' => fake('id_ID')->sentence(10),
            'photoUrl' => 'img/items/wallet.svg',
            'status' => 'FOUND_REPORTED',
            'createdAt' => now(),
        ];
    }

    public function diterima(?string $securityId = null): static
    {
        return $this->state(fn () => ['receivedAt' => now(), 'receivedBy' => $securityId ?? User::factory()->security()]);
    }

    public function kandidat(): static { return $this->state(fn () => ['status' => 'MATCH_CANDIDATE']); }
}