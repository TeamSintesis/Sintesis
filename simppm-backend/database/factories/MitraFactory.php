<?php

namespace Database\Factories;

use App\Models\Mitra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Mitra (Mitra Eksternal).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Mitra>
 */
class MitraFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Mitra>
     */
    protected $model = Mitra::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_mitra' => fake()->name(),
            'jenis_mitra' => fake()->randomElement(['industri', 'pemda', 'masyarakat', 'pt_lain']),
            'kontak' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
        ];
    }
}
