<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** [AR] Factory User — TODO AR: isi definition() sesuai DDL (email wajib domain UB). */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // TODO AR: namaLengkap, nim (unik), email @student.ub.ac.id (unik),
        //          username (unik), passwordHash = Hash::make('findkom123'),
        //          kontak, role CIVITAS, createdAt.
        return [];
    }

    // TODO AR (opsional): state security() untuk role SECURITY + email @ub.ac.id.
}