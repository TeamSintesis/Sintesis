<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\BantuanUjiJsonApi;
use Tests\TestCase;

/**
 * Feature Test JSON:API untuk resource "anggota-tim" (Anggota Tim Proposal).
 *
 * Kategori: dikelola oleh dosen pengusul proposal terkait dan
 * pengelola LPPM.
 *
 * Dibuat otomatis oleh spec/generate_tests.py berdasarkan
 * spec/entities.json, agar aturan validasi & RBAC yang diuji selalu
 * konsisten dengan definisi entitas yang sebenarnya.
 */
class AnggotaTimTest extends TestCase
{
    use RefreshDatabase;
    use BantuanUjiJsonApi;

    /**
     * Buat entitas terkait (FK) yang diperlukan agar data
     * "anggota-tim" valid, lalu kembalikan sebagai array
     * relationships JSON:API.
     */
    protected function relasiPendukung(): array
    {
        $proposal = \App\Models\Proposal::factory()->create();
        $dosen = \App\Models\Dosen::factory()->create();
        $mahasiswa = \App\Models\Mahasiswa::factory()->create();

        return [
                'proposal' => ['data' => ['type' => 'proposal', 'id' => (string) $proposal->id]],
                'dosen' => ['data' => ['type' => 'dosen', 'id' => (string) $dosen->id]],
                'mahasiswa' => ['data' => ['type' => 'mahasiswa', 'id' => (string) $mahasiswa->id]],
        ];
    }


    /**
     * Bangun dokumen JSON:API valid untuk permintaan create resource ini.
     */
    protected function payloadValid(): array
    {
        return [
            'data' => [
                'type' => 'anggota-tim',
                'relationships' => $this->relasiPendukung(),
                'attributes' => [
            'peran' => 'Contoh peran',
                ],
            ],
        ];
    }


    /**
     * Permintaan tanpa autentikasi harus ditolak dengan status 401.
     */
    public function test_tanpa_autentikasi_ditolak(): void
    {
        $response = $this->getJson('/api/v1/anggota-tim', $this->headerJsonApi());

        $response->assertStatus(401);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang melihat daftar data
     * pada seluruh kategori entitas SIMPPM.
     */
    public function test_pengelola_dapat_melihat_daftar(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->getJson('/api/v1/anggota-tim', $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Pengelola LPPM (admin_lppm) selalu berwenang membuat data baru
     * pada seluruh kategori entitas SIMPPM (lihat AnggotaTimPolicy::create()).
     */
    public function test_pengelola_dapat_membuat_data(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $response = $this->postJson('/api/v1/anggota-tim', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(201);
    }

    /**
     * Peran 'mahasiswa' tidak termasuk peran yang berwenang membuat
     * data "anggota-tim" (lihat AnggotaTimPolicy::create()), sehingga
     * harus ditolak dengan status 403.
     */
    public function test_peran_tidak_berwenang_ditolak_saat_membuat(): void
    {
        $this->sebagaiPeran(User::PERAN_MAHASISWA);

        $response = $this->postJson('/api/v1/anggota-tim', $this->payloadValid(), $this->headerJsonApi());

        $response->assertStatus(403);
    }

    /**
     * Data yang berhasil dibuat harus dapat dilihat detailnya oleh
     * pengelola LPPM.
     */
    public function test_pengelola_dapat_melihat_detail(): void
    {
        $this->sebagaiPeran(User::PERAN_ADMIN_LPPM);

        $dibuat = $this->postJson('/api/v1/anggota-tim', $this->payloadValid(), $this->headerJsonApi());
        $dibuat->assertStatus(201);
        $id = $dibuat->json('data.id');

        $response = $this->getJson("/api/v1/anggota-tim/{$id}", $this->headerJsonApi());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $id);
    }
}
