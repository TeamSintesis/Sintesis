<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Pedoman;
use App\Models\Renstra;
use App\Models\SaranaPrasarana;
use App\Models\SkemaPendanaan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder utama SIMPPM.
 *
 * Membuat satu akun demo untuk masing-masing dari 8 peran (aktor) yang
 * didefinisikan pada Use_Case_Diagram_SIMPPM.md, agar frontend Svelte
 * (yang uncoupled) dapat diuji-coba/didemokan lintas peran tanpa perlu
 * mendaftar manual. Setiap akun demo memakai kata sandi yang sama
 * ("password") -- HANYA untuk lingkungan pengembangan/demo, WAJIB diganti
 * atau dihapus sebelum production.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data pendukung agar peran dosen/mahasiswa/mitra_eksternal
        // memiliki data kepemilikan (ownership) yang dapat diuji oleh
        // Policy (mis. dosen hanya dapat mengubah proposal miliknya).
        $dosen = Dosen::factory()->create([
            'nama' => 'Dr. Budi Santoso',
        ]);
        $mahasiswa = Mahasiswa::factory()->create([
            'nama' => 'Siti Aminah',
        ]);
        $mitra = Mitra::factory()->create([
            'nama_mitra' => 'PT Sinergi Nusantara',
        ]);

        // Data rujukan fase Masukan -- disediakan agar entitas fase Proses
        // (mis. Proposal yang mewajibkan skema_pendanaan_id, atau
        // PetaJalan yang mewajibkan renstra_id) dapat langsung dibuat lewat
        // frontend tanpa harus mengisi data induk secara manual dahulu.
        Renstra::factory()->count(2)->create();
        SkemaPendanaan::factory()->count(3)->create();
        Pedoman::factory()->count(2)->create();
        SaranaPrasarana::factory()->count(3)->create();

        $akunDemo = [
            ['peran' => User::PERAN_ADMIN_LPPM, 'name' => 'Admin LPPM', 'email' => 'admin.lppm@simppm.test'],
            ['peran' => User::PERAN_KETUA_LPPM, 'name' => 'Ketua LPPM', 'email' => 'ketua.lppm@simppm.test'],
            ['peran' => User::PERAN_DOSEN, 'name' => $dosen->nama, 'email' => 'dosen@simppm.test', 'dosen_id' => $dosen->id],
            ['peran' => User::PERAN_MAHASISWA, 'name' => $mahasiswa->nama, 'email' => 'mahasiswa@simppm.test', 'mahasiswa_id' => $mahasiswa->id],
            ['peran' => User::PERAN_REVIEWER, 'name' => 'Reviewer', 'email' => 'reviewer@simppm.test'],
            ['peran' => User::PERAN_PRODI_GKM, 'name' => 'Prodi/GKM', 'email' => 'prodi.gkm@simppm.test'],
            ['peran' => User::PERAN_PIMPINAN, 'name' => 'Pimpinan', 'email' => 'pimpinan@simppm.test'],
            ['peran' => User::PERAN_MITRA_EKSTERNAL, 'name' => $mitra->nama_mitra, 'email' => 'mitra@simppm.test', 'mitra_id' => $mitra->id],
        ];

        foreach ($akunDemo as $data) {
            User::factory()->create($data);
        }
    }
}
