<?php

namespace Database\Factories;

use App\Models\AnggotaTim;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model AnggotaTim (Anggota Tim Proposal).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<AnggotaTim>
 */
class AnggotaTimFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<AnggotaTim>
     */
    protected $model = AnggotaTim::class;

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
            'mahasiswa_id' => Mahasiswa::factory(),
            'peran' => fake()->text(50),
        ];
    }
}
