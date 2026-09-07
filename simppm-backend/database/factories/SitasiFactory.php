<?php

namespace Database\Factories;

use App\Models\Sitasi;
use App\Models\Dosen;
use App\Models\Publikasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Sitasi (Sitasi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Sitasi>
 */
class SitasiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Sitasi>
     */
    protected $model = Sitasi::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'publikasi_id' => Publikasi::factory(),
            'dosen_id' => Dosen::factory(),
            'jumlah_sitasi' => fake()->numberBetween(1, 5),
            'tahun' => fake()->numberBetween(2022, 2027),
            'sumber' => fake()->randomElement(['google_scholar', 'sinta', 'scopus']),
        ];
    }
}
