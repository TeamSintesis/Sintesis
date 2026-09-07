<?php

namespace App\Policies;

use App\Models\Hki;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Hki (Kekayaan Intelektual).
 *
 * Fase Luaran (Kekayaan Intelektual): dikelola oleh dosen pengusul proposal
 * terkait (sebagai pelapor luaran) dan diverifikasi oleh pengelola
 * LPPM. Peran pemantau (prodi_gkm/pimpinan) memiliki akses baca
 * untuk keperluan evaluasi capaian.
 */
class HkiPolicy
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
    public function view(User $user, Hki $hki): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $hki);
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
    public function update(User $user, Hki $hki): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $hki);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, Hki $hki): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $hki);
    }

}
