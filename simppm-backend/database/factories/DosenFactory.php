<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Dosen (Dosen/Peneliti).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Dosen>
 */
class DosenFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Dosen>
     */
    protected $model = Dosen::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prodi_id' => Prodi::factory(),
            'nidn' => fake()->unique()->numerify('##########'),
            'nama' => fake()->name(),
            'jabatan_fungsional' => fake()->text(50),
            'bidang_kepakaran' => fake()->text(60),
            'id_sinta' => fake()->unique()->numerify('##########'),
            'scopus_id' => fake()->unique()->numerify('##########'),
            'orcid' => fake()->unique()->numerify('##########'),
        ];
    }
}
