<?php

namespace Database\Factories;

use App\Models\Publikasi;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Publikasi (Publikasi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Publikasi>
 */
class PublikasiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Publikasi>
     */
    protected $model = Publikasi::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'judul' => fake()->sentence(8),
            'jurnal_prosiding' => fake()->text(60),
            'indeksasi' => fake()->randomElement(['scopus', 'sinta_1', 'sinta_2', 'sinta_3', 'sinta_4', 'sinta_5', 'sinta_6', 'nasional_non_sinta']),
            'tahun' => fake()->numberBetween(2022, 2027),
            'doi' => fake()->unique()->numerify('##########'),
            'lisensi' => fake()->text(20),
        ];
    }
}
