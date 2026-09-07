<?php

namespace Database\Factories;

use App\Models\Kontrak;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Kontrak (Kontrak Penugasan).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Kontrak>
 */
class KontrakFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Kontrak>
     */
    protected $model = Kontrak::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'nomor_sk' => fake()->unique()->numerify('###/UN/NOMOR_SK/####'),
            'tanggal_kontrak' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'nilai_kontrak' => fake()->numberBetween(1_000_000, 500000000),
        ];
    }
}
