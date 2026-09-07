<?php

namespace App\Policies;

use App\Models\AnggotaTim;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model AnggotaTim (Anggota Tim Proposal).
 *
 * Fase Proses (Anggota Tim Proposal): dikelola oleh dosen pengusul proposal
 * terkait (ditelusuri melalui relasi proposal) dan oleh pengelola
 * LPPM. Peran pemantau (prodi_gkm/pimpinan) hanya memiliki akses
 * baca untuk keperluan monitoring.
 */
class AnggotaTimPolicy
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
    public function view(User $user, AnggotaTim $anggotaTim): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $anggotaTim);
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
    public function update(User $user, AnggotaTim $anggotaTim): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $anggotaTim);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, AnggotaTim $anggotaTim): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $anggotaTim);
    }

}
