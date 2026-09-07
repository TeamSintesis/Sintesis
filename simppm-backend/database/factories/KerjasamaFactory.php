<?php

namespace Database\Factories;

use App\Models\Kerjasama;
use App\Models\Mitra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Kerjasama (Kerja Sama).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Kerjasama>
 */
class KerjasamaFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Kerjasama>
     */
    protected $model = Kerjasama::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mitra_id' => Mitra::factory(),
            'ruang_lingkup' => fake()->text(60),
            'tanggal_mulai' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'tanggal_akhir' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['aktif', 'berakhir', 'diperpanjang']),
        ];
    }
}
