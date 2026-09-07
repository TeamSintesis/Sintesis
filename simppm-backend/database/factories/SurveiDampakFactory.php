<?php

namespace Database\Factories;

use App\Models\SurveiDampak;
use App\Models\Mitra;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model SurveiDampak (Survei Dampak).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<SurveiDampak>
 */
class SurveiDampakFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<SurveiDampak>
     */
    protected $model = SurveiDampak::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mitra_id' => Mitra::factory(),
            'proposal_id' => Proposal::factory(),
            'hasil' => fake()->realText(200),
            'testimoni' => fake()->realText(200),
        ];
    }
}
