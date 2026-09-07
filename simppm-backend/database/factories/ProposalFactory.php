<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\Dosen;
use App\Models\PetaJalan;
use App\Models\SkemaPendanaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Proposal (Proposal Penelitian/PkM).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<Proposal>
     */
    protected $model = Proposal::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dosen_id' => Dosen::factory(),
            'skema_pendanaan_id' => SkemaPendanaan::factory(),
            'peta_jalan_id' => PetaJalan::factory(),
            'judul' => fake()->sentence(8),
            'jenis' => fake()->randomElement(['penelitian', 'pkm']),
            'tahun_usulan' => fake()->numberBetween(2022, 2027),
            'status' => 'draft',
            'rab_total' => fake()->numberBetween(1_000_000, 500000000),
            'berisiko_etik' => false,
        ];
    }

    /**
     * State: Proposal telah diajukan (melewati tahap draft).
     */
    public function diajukan(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'diajukan']);
    }

    /**
     * State: Proposal sedang direview.
     */
    public function direview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'direview']);
    }

    /**
     * State: Proposal ditandai berisiko etik (memerlukan Klirens Etik).
     */
    public function berisikoEtik(): static
    {
        return $this->state(fn (array $attributes) => ['berisiko_etik' => true]);
    }
}
