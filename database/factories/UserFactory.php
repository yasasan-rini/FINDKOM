<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $nama = fake('id_ID')->name();
        $unik = fake()->unique()->numberBetween(1, 999999);

        return [
            'namaLengkap' => $nama,
            'nim' => (string) fake()->unique()->numerify('2551504########'),
            'email' => "user{$unik}@student.ub.ac.id",
            'username' => "user{$unik}",
            'passwordHash' => Hash::make('findkom123'),
            'kontak' => '08'.fake()->numerify('##########'),
            'role' => 'CIVITAS',
            'createdAt' => now(),
        ];
    }

    public function security(): static
    {
        $unik = fake()->unique()->numberBetween(1, 999999);
        return $this->state(fn () => [
            'role' => 'SECURITY', 'nim' => null,
            'email' => "security{$unik}@ub.ac.id", 'username' => "security{$unik}",
        ]);
    }
}