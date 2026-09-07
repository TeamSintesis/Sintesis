<?php

namespace Database\Factories;

use App\Models\ProdukAdopsi;
use App\Models\Mitra;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model ProdukAdopsi (Produk/Purwarupa Adopsi).
 *
 * Menghasilkan data uji yang realistis (locale Bahasa Indonesia) untuk
 * keperluan pengujian otomatis dan basis data awal (seeding).
 *
 * @extends Factory<ProdukAdopsi>
 */
class ProdukAdopsiFactory extends Factory
{
    /**
     * Nama model yang bersesuaian dengan factory ini.
     *
     * @var class-string<ProdukAdopsi>
     */
    protected $model = ProdukAdopsi::class;

    /**
     * Definisikan nilai bawaan (default) atribut model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'mitra_id' => Mitra::factory(),
            'nama_produk' => fake()->name(),
            'bukti_adopsi' => fake()->url(),
        ];
    }
}
