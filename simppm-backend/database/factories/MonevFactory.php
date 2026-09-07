<?php

namespace Database\Factories;

use App\Models\Monev;
use App\Models\Dosen;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Monev (Monitoring dan Evaluasi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Monev>
 */
class MonevFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Monev>
     */
    protected $model = Monev::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'dosen_id' => Dosen::factory(),
            'catatan' => fake()->realText(200),
            'status' => fake()->randomElement(['terjadwal', 'selesai']),
        ];
    }
}
