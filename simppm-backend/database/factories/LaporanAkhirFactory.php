<?php

namespace Database\Factories;

use App\Models\LaporanAkhir;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model LaporanAkhir (Laporan Akhir).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<LaporanAkhir>
 */
class LaporanAkhirFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<LaporanAkhir>
     */
    protected $model = LaporanAkhir::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'dokumen_url' => fake()->url(),
            'tanggal_submit' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'status_verifikasi' => fake()->randomElement(['belum', 'terverifikasi']),
        ];
    }
}
