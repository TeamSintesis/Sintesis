<?php

namespace Database\Factories;

use App\Models\Hki;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Hki (Kekayaan Intelektual).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Hki>
 */
class HkiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Hki>
     */
    protected $model = Hki::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'jenis' => fake()->randomElement(['paten', 'hak_cipta', 'desain_industri', 'merek', 'lainnya']),
            'status' => fake()->randomElement(['diajukan', 'diperiksa_substantif', 'terbit', 'ditolak']),
            'nomor_sertifikat' => fake()->unique()->numerify('###/UN/NOMOR_SERTIFIKAT/####'),
        ];
    }
}
