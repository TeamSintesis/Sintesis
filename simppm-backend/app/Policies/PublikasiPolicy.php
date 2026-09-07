<?php

namespace App\Policies;

use App\Models\Publikasi;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Publikasi (Publikasi).
 *
 * Fase Luaran (Publikasi): dikelola oleh dosen pengusul proposal
 * terkait (sebagai pelapor luaran) dan diverifikasi oleh pengelola
 * LPPM. Peran pemantau (prodi_gkm/pimpinan) memiliki akses baca
 * untuk keperluan evaluasi capaian.
 */
class PublikasiPolicy
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
    public function view(User $user, Publikasi $publikasi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $publikasi);
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
    public function update(User $user, Publikasi $publikasi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $publikasi);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, Publikasi $publikasi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $publikasi);
    }

}
