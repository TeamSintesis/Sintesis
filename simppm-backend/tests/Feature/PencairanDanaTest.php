<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BantuanUjiJsonApi;
use Tests\TestCase;

/**
 * Feature Test JSON:API untuk resource "pencairan-dana" (Pencairan Dana).
 *
 * Kategori: sepenuhnya dikelola oleh pengelola LPPM.
 *
 * Dibuat otomatis oleh spec/generate_tests.py berdasarkan
 * spec/entities.json, agar aturan validasi & RBAC yang diuji selalu
 * konsisten dengan definisi entitas yang sebenarnya.
 */
class PencairanDanaTest extends TestCase
{
    use RefreshDatabase;
    use BantuanUjiJsonApi;

    /**
     * Buat entitas terkait (FK) yang diperlukan agar data
     * "pencairan-dana" valid, lalu kembalikan sebagai array
     * relationships JSON:API.
     */
    protected function relasiPendukung(): array
    {
        $kontrak = \App\Models\Kontrak::factory()->create();

        return [
                'kontrak' => ['data' => ['type' => 'kontrak', 'id' => (string) $kontrak->id]],
        ];
    }


    /**
     * Bangun dokumen JSON:API valid untuk permintaan create resource ini.
     */
    protected function payloadValid(): array
    {
        return [
            'data' => [
                'type' => 'pencairan-dana',
                'relationships' => $this->relasiPendukung(),
                'attributes' => [
            'termin' => 1,
            'jumlah' => '5000000.00',
            'tanggal' => '2026-01-15',
            'status' => 'dijadwalkan',
                ],
            ],
        ];
    }


    /**
     * Permintaan tanpa autentikasi harus ditolak dengan status 401.
     */
    public function test_tanpa_autentikasi_ditolak(): void
    {
        $response = $this->getJson('/api/v1/pencairan-dana', $this->headerJsonApi());

        $response->assertStatus(401);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang melihat daftar data
     * pada seluruh kategori entitas SIMPPM.
     */
    public function test_pengelola_dapat_melihat_daftar(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->getJson('/api/v1/pencairan-dana', $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang membuat data baru
     * pada seluruh kategori entitas SIMPPM (lihat PencairanDanaPolicy::create()).
     */
    public function test_pengelola_dapat_membuat_data(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->postJson('/api/v1/pencairan-dana', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(201);
    }

    /**
     * Peran 'dosen' tidak termasuk peran yang berwenang membuat
     * data "pencairan-dana" (lihat PencairanDanaPolicy::create()), sehingga
     * harus ditolak dengan status 403.
     */
    public function test_peran_tidak_berwenang_ditolak_saat_membuat(): void
    {
        $this->sebagaiPeran(User::PERAN_DOSEN);

        $response = $this->postJson('/api/v1/pencairan-dana', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(403);
    }

    /**
     * Data yang berhasil dibuat harus dapat dilihat detailnya oleh
     * pengelola LPPM.
     */
    public function test_pengelola_dapat_melihat_detail(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $dibuat = $this->postJson('/api/v1/pencairan-dana', $this->payloadValid(), $this->headerJsonApi());
        $dibuat->assertStatus(201);
        $id = $dibuat->json('data.id');

        $response = $this->getJson("/api/v1/pencairan-dana/{$id}", $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $id);
    }
}
