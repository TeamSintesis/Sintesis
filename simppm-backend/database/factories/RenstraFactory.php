<?php

namespace Database\Factories;

use App\Models\Renstra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Renstra (Rencana Strategis).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Renstra>
 */
class RenstraFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Renstra>
     */
    protected $model = Renstra::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis' => fake()->randomElement(['penelitian', 'pkm']),
            'periode_mulai' => fake()->numberBetween(2022, 2027),
            'periode_akhir' => fake()->numberBetween(2022, 2027),
            'dokumen_url' => fake()->url(),
        ];
    }
}
