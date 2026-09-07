<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BantuanUjiJsonApi;
use Tests\TestCase;

/**
 * Feature Test JSON:API untuk resource "laporan-akhir" (Laporan Akhir).
 *
 * Kategori: Fase Luaran -- dikelola oleh dosen pengusul dan
 * pengelola LPPM.
 *
 * Dibuat otomatis oleh spec/generate_tests.py berdasarkan
 * spec/entities.json, agar aturan validasi & RBAC yang diuji selalu
 * konsisten dengan definisi entitas yang sebenarnya.
 */
class LaporanAkhirTest extends TestCase
{
    use RefreshDatabase;
    use BantuanUjiJsonApi;

    /**
     * Buat entitas terkait (FK) yang diperlukan agar data
     * "laporan-akhir" valid, lalu kembalikan sebagai array
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
                'type' => 'laporan-akhir',
                'relationships' => $this->relasiPendukung(),
                'attributes' => [
            'dokumenUrl' => 'https://contoh.simppm.test/berkas.pdf',
            'tanggalSubmit' => '2026-01-15',
            'statusVerifikasi' => 'belum',
                ],
            ],
        ];
    }


    /**
     * Permintaan tanpa autentikasi harus ditolak dengan status 401.
     */
    public function test_tanpa_autentikasi_ditolak(): void
    {
        $response = $this->getJson('/api/v1/laporan-akhir', $this->headerJsonApi());

        $response->assertStatus(401);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang melihat daftar data
     * pada seluruh kategori entitas SIMPPM.
     */
    public function test_pengelola_dapat_melihat_daftar(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->getJson('/api/v1/laporan-akhir', $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang membuat data baru
     * pada seluruh kategori entitas SIMPPM (lihat LaporanAkhirPolicy::create()).
     */
    public function test_pengelola_dapat_membuat_data(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->postJson('/api/v1/laporan-akhir', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(201);
    }

    /**
     * Peran 'mahasiswa' tidak termasuk peran yang berwenang membuat
     * data "laporan-akhir" (lihat LaporanAkhirPolicy::create()), sehingga
     * harus ditolak dengan status 403.
     */
    public function test_peran_tidak_berwenang_ditolak_saat_membuat(): void
    {
        $this->sebagaiPeran(User::PERAN_MAHASISWA);

        $response = $this->postJson('/api/v1/laporan-akhir', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(403);
    }

    /**
     * Data yang berhasil dibuat harus dapat dilihat detailnya oleh
     * pengelola LPPM.
     */
    public function test_pengelola_dapat_melihat_detail(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $dibuat = $this->postJson('/api/v1/laporan-akhir', $this->payloadValid(), $this->headerJsonApi());
        $dibuat->assertStatus(201);
        $id = $dibuat->json('data.id');

        $response = $this->getJson("/api/v1/laporan-akhir/{$id}", $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $id);
    }
}
