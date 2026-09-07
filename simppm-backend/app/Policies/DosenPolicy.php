<?php

namespace App\Policies;

use App\Models\Dosen;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Dosen (Dosen/Peneliti).
 *
 * Fase Masukan: data master/referensi bersifat baca-terbuka bagi
 * seluruh pengguna terautentikasi, sedangkan penulisan (create/
 * update/delete) dibatasi hanya untuk pengelola LPPM (admin_lppm
 * atau ketua_lppm).
 */
class DosenPolicy
{
    use BantuanOtorisasi;

    /**
     * Semua pengguna terautentikasi boleh melihat daftar data master.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua pengguna terautentikasi boleh melihat detail data master.
     */
    public function view(User $user, Dosen $dosen): bool
    {
        return true;
    }

    /**
     * Hanya pengelola LPPM yang boleh menambah data master baru.
     */
    public function create(User $user): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh mengubah data master.
     */
    public function update(User $user, Dosen $dosen): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data master.
     */
    public function delete(User $user, Dosen $dosen): bool
    {
        return $this->adalahPengelola($user);
    }

}
