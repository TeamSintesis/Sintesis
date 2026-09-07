<?php

namespace Database\Factories;

use App\Models\PetaJalan;
use App\Models\Prodi;
use App\Models\Renstra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model PetaJalan (Peta Jalan Penelitian/PkM).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<PetaJalan>
 */
class PetaJalanFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<PetaJalan>
     */
    protected $model = PetaJalan::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'renstra_id' => Renstra::factory(),
            'prodi_id' => Prodi::factory(),
            'bidang_keilmuan' => fake()->text(60),
            'tahun' => fake()->numberBetween(2022, 2027),
        ];
    }
}
