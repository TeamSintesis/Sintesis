<?php

namespace App\Policies;

use App\Models\Sitasi;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Sitasi (Sitasi).
 *
 * Fase Dampak (Sitasi): dikelola oleh dosen pemilik data secara
 * langsung serta oleh pengelola LPPM. Peran pemantau memiliki akses
 * baca untuk keperluan evaluasi dampak jangka panjang.
 */
class SitasiPolicy
{
    use BantuanOtorisasi;

    /**
     * Semua pengguna terautentikasi boleh melihat daftar data.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Dosen pengusul proposal terkait, pengelola LPPM, dan pemantau boleh
     * melihat detail data.
     */
    public function view(User $user, Sitasi $sitasi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $sitasi);
    }

    /**
     * Dosen (untuk data miliknya) atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN) || $this->adalahPengelola($user);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, Sitasi $sitasi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $sitasi);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, Sitasi $sitasi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $sitasi);
    }

}
