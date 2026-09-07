<?php

namespace Tests\Feature\Concerns;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Trait bantuan yang dipakai bersama oleh seluruh Feature Test JSON:API
 * SIMPPM, agar tiap kelas uji tidak perlu mengulang logika pembuatan
 * pengguna per peran dan header JSON:API.
 *
 * Menyimpan logika bersama di satu trait menghindari duplikasi pada 27
 * kelas Feature Test (satu per entitas) dan menjaga konsistensi cara
 * setiap peran diautentikasi pada pengujian.
 */
trait BantuanUjiJsonApi
{
    /**
     * Header standar yang wajib disertakan pada setiap permintaan
     * JSON:API (Content-Type & Accept sesuai spesifikasi JSON:API).
     *
     * @return array<string, string>
     */
    protected function headerJsonApi(): array
    {
        return [
            'Content-Type' => 'application/vnd.api+json',
            'Accept' => 'application/vnd.api+json',
        ];
    }

    /**
     * Autentikasikan permintaan pengujian berikutnya sebagai pengguna
     * dengan peran tertentu, menggunakan guard Sanctum bawaan untuk
     * pengujian (tidak perlu memanggil endpoint /auth/login secara nyata).
     *
     * Untuk peran 'dosen', 'mahasiswa', dan 'mitra_eksternal', akan
     * otomatis dibuatkan (atau dipakai) entitas Dosen/Mahasiswa/Mitra
     * terkait agar aturan kepemilikan (ownership) pada Policy dapat diuji.
     */
    protected function sebagaiPeran(string $peran, array $atributTambahan = []): User
    {
        $atribut = array_merge(['peran' => $peran], $atributTambahan);

        if ($peran === User::PERAN_DOSEN && ! isset($atribut['dosen_id'])) {
            $atribut['dosen_id'] = Dosen::factory()->create()->id;
        }

        if ($peran === User::PERAN_MAHASISWA && ! isset($atribut['mahasiswa_id'])) {
            $atribut['mahasiswa_id'] = Mahasiswa::factory()->create()->id;
        }

        if ($peran === User::PERAN_MITRA_EKSTERNAL && ! isset($atribut['mitra_id'])) {
            $atribut['mitra_id'] = Mitra::factory()->create()->id;
        }

        $user = User::factory()->create($atribut);

        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    /**
     * Ambil objek Dosen yang terkait dengan pengguna hasil sebagaiPeran()
     * berperan 'dosen', untuk dipakai membangun data uji kepemilikan.
     */
    protected function dosenMilik(User $user): Dosen
    {
        return Dosen::findOrFail($user->dosen_id);
    }
}
