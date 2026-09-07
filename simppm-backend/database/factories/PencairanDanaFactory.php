<?php

namespace Database\Factories;

use App\Models\PencairanDana;
use App\Models\Kontrak;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model PencairanDana (Pencairan Dana).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<PencairanDana>
 */
class PencairanDanaFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<PencairanDana>
     */
    protected $model = PencairanDana::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kontrak_id' => Kontrak::factory(),
            'termin' => fake()->numberBetween(1, 5),
            'jumlah' => fake()->numberBetween(1_000_000, 500000000),
            'tanggal' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['dijadwalkan', 'dicairkan', 'tertunda']),
        ];
    }
}
