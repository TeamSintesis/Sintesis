<?php

namespace Database\Factories;

use App\Models\KlirensEtik;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model KlirensEtik (Klirens Etik).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<KlirensEtik>
 */
class KlirensEtikFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<KlirensEtik>
     */
    protected $model = KlirensEtik::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'status' => fake()->randomElement(['diajukan', 'ditinjau', 'disetujui', 'ditolak']),
            'nomor_sertifikat' => fake()->unique()->numerify('###/UN/NOMOR_SERTIFIKAT/####'),
            'tanggal_terbit' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ];
    }
}
