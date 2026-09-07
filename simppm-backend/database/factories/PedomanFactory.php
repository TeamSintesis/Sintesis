<?php

namespace Database\Factories;

use App\Models\Pedoman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Pedoman (Pedoman dan Kode Etik).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Pedoman>
 */
class PedomanFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Pedoman>
     */
    protected $model = Pedoman::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis' => fake()->text(50),
            'versi' => fake()->bothify('v#.#'),
            'tanggal_berlaku' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'dokumen_url' => fake()->url(),
        ];
    }
}
