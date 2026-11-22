<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** [VP] Factory Category — TODO VP: isi definition() (name unik). */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        // TODO VP: return ['name' => 'Kategori ' . angka unik];
        return [];
    }
}