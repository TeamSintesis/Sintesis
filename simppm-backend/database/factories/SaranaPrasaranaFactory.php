<?php

namespace Database\Factories;

use App\Models\SaranaPrasarana;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model SaranaPrasarana (Sarana dan Prasarana).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<SaranaPrasarana>
 */
class SaranaPrasaranaFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<SaranaPrasarana>
     */
    protected $model = SaranaPrasarana::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_sarpras' => fake()->name(),
            'lokasi' => fake()->text(60),
            'status' => fake()->randomElement(['tersedia', 'digunakan', 'rusak']),
        ];
    }
}
