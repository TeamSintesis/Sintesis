<?php

namespace Database\Factories;

use App\Models\Spj;
use App\Models\LaporanAkhir;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Spj (Surat Pertanggungjawaban).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Spj>
 */
class SpjFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Spj>
     */
    protected $model = Spj::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'laporan_akhir_id' => LaporanAkhir::factory(),
            'jumlah_realisasi' => fake()->numberBetween(1_000_000, 500000000),
            'dokumen_url' => fake()->url(),
        ];
    }
}
