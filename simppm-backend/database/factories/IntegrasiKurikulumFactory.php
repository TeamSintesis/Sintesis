<?php

namespace Database\Factories;

use App\Models\IntegrasiKurikulum;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model IntegrasiKurikulum (Integrasi Kurikulum).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<IntegrasiKurikulum>
 */
class IntegrasiKurikulumFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<IntegrasiKurikulum>
     */
    protected $model = IntegrasiKurikulum::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'mata_kuliah' => fake()->text(60),
            'rps_tautan' => fake()->url(),
        ];
    }
}
