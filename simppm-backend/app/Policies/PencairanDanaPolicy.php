<?php

namespace App\Policies;

use App\Models\PencairanDana;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model PencairanDana (Pencairan Dana).
 *
 * Fase Proses (Pencairan Dana): sepenuhnya dikelola (create/update/delete)
 * oleh pengelola LPPM (admin_lppm/ketua_lppm). Dosen pengusul
 * proposal terkait dan peran pemantau hanya memiliki akses baca.
 */
class PencairanDanaPolicy
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
    public function view(User $user, PencairanDana $pencairanDana): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $pencairanDana);
    }

    /**
     * Hanya pengelola LPPM yang boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh mengubah data.
     */
    public function update(User $user, PencairanDana $pencairanDana): bool
    {
        return $this->adalahPengelola($user);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data.
     */
    public function delete(User $user, PencairanDana $pencairanDana): bool
    {
        return $this->adalahPengelola($user);
    }

}
