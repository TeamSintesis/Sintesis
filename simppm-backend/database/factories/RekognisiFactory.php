<?php

namespace Database\Factories;

use App\Models\Rekognisi;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Rekognisi (Rekognisi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Rekognisi>
 */
class RekognisiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Rekognisi>
     */
    protected $model = Rekognisi::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dosen_id' => Dosen::factory(),
            'jenis' => fake()->text(60),
            'deskripsi' => fake()->realText(200),
            'tahun' => fake()->numberBetween(2022, 2027),
        ];
    }
}
