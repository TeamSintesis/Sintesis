<?php

namespace App\Policies;

use App\Models\PetaJalan;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model PetaJalan (Peta Jalan Penelitian/PkM).
 *
 * Fase Masukan: data master/referensi bersifat baca-terbuka bagi
 * seluruh pengguna terautentikasi, sedangkan penulisan (create/
 * update/delete) dibatasi hanya untuk pengelola LPPM (admin_lppm
 * atau ketua_lppm).
 */
class PetaJalanPolicy
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
    public function view(User $user, PetaJalan $petaJalan): bool
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
    public function update(User $user, PetaJalan $petaJalan): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data master.
     */
    public function delete(User $user, PetaJalan $petaJalan): bool
    {
        return $this->adalahPengelola($user);
    }

}
