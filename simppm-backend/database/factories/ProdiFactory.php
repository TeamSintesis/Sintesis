<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Prodi (Program Studi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Prodi>
 */
class ProdiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Prodi>
     */
    protected $model = Prodi::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_prodi' => fake()->name(),
            'fakultas' => fake()->text(60),
        ];
    }
}
