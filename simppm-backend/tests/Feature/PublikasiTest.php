<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BantuanUjiJsonApi;
use Tests\TestCase;

/**
 * Feature Test JSON:API untuk resource "publikasi" (Publikasi).
 *
 * Kategori: Fase Luaran -- dikelola oleh dosen pengusul dan
 * pengelola LPPM.
 *
 * Dibuat otomatis oleh spec/generate_tests.py berdasarkan
 * spec/entities.json, agar aturan validasi & RBAC yang diuji selalu
 * konsisten dengan definisi entitas yang sebenarnya.
 */
class PublikasiTest extends TestCase
{
    use RefreshDatabase;
    use BantuanUjiJsonApi;

    /**
     * Buat entitas terkait (FK) yang diperlukan agar data
     * "publikasi" valid, lalu kembalikan sebagai array
     * relationships JSON:API.
     */
    protected function relasiPendukung(): array
    {
        $proposal = \App\Models\Proposal::factory()->create();

        return [
                'proposal' => ['data' => ['type' => 'proposal', 'id' => (string) $proposal->id]],
        ];
    }


    /**
     * Bangun dokumen JSON:API valid untuk permintaan create resource ini.
     */
    protected function payloadValid(): array
    {
        return [
            'data' => [
                'type' => 'publikasi',
                'relationships' => $this->relasiPendukung(),
                'attributes' => [
            'judul' => 'Contoh judul',
            'jurnalProsiding' => 'Contoh jurnal prosiding',
            'indeksasi' => 'scopus',
            'tahun' => '2026',
            'doi' => 'Contoh doi',
            'lisensi' => 'Contoh lisensi',
                ],
            ],
        ];
    }


    /**
     * Permintaan tanpa autentikasi harus ditolak dengan status 401.
     */
    public function test_tanpa_autentikasi_ditolak(): void
    {
        $response = $this->getJson('/api/v1/publikasi', $this->headerJsonApi());

        $response->assertStatus(401);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang melihat daftar data
     * pada seluruh kategori entitas SIMPPM.
     */
    public function test_pengelola_dapat_melihat_daftar(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->getJson('/api/v1/publikasi', $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang membuat data baru
     * pada seluruh kategori entitas SIMPPM (lihat PublikasiPolicy::create()).
     */
    public function test_pengelola_dapat_membuat_data(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->postJson('/api/v1/publikasi', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(201);
    }

    /**
     * Peran 'mahasiswa' tidak termasuk peran yang berwenang membuat
     * data "publikasi" (lihat PublikasiPolicy::create()), sehingga
     * harus ditolak dengan status 403.
     */
    public function test_peran_tidak_berwenang_ditolak_saat_membuat(): void
    {
        $this->sebagaiPeran(User::PERAN_MAHASISWA);

        $response = $this->postJson('/api/v1/publikasi', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(403);
    }

    /**
     * Data yang berhasil dibuat harus dapat dilihat detailnya oleh
     * pengelola LPPM.
     */
    public function test_pengelola_dapat_melihat_detail(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $dibuat = $this->postJson('/api/v1/publikasi', $this->payloadValid(), $this->headerJsonApi());
        $dibuat->assertStatus(201);
        $id = $dibuat->json('data.id');

        $response = $this->getJson("/api/v1/publikasi/{$id}", $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $id);
    }
}
