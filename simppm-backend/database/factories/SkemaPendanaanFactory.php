<?php

namespace Database\Factories;

use App\Models\SkemaPendanaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model SkemaPendanaan (Skema Pendanaan).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<SkemaPendanaan>
 */
class SkemaPendanaanFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<SkemaPendanaan>
     */
    protected $model = SkemaPendanaan::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_skema' => fake()->name(),
            'jenis' => fake()->randomElement(['internal', 'eksternal']),
            'plafon_dana' => fake()->numberBetween(1_000_000, 500000000),
            'sumber_dana' => fake()->text(60),
        ];
    }
}
