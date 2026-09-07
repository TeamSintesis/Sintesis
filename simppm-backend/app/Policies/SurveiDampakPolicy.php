<?php

namespace App\Policies;

use App\Models\SurveiDampak;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model SurveiDampak (Survei Dampak).
 *
 * Fase Dampak (Survei Dampak): diisi/dikelola oleh mitra eksternal
 * pemilik data serta oleh pengelola LPPM. Peran pemantau memiliki
 * akses baca untuk keperluan evaluasi dampak kerja sama.
 */
class SurveiDampakPolicy
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
     * Mitra pemilik data, pengelola LPPM, dan pemantau boleh melihat
     * detail data.
     */
    public function view(User $user, SurveiDampak $surveiDampak): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikMitra($user, $surveiDampak);
    }

    /**
     * Mitra eksternal atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_MITRA_EKSTERNAL) || $this->adalahPengelola($user);
    }

    /**
     * Mitra pemilik data atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, SurveiDampak $surveiDampak): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikMitra($user, $surveiDampak);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data mitra/dampak.
     */
    public function delete(User $user, SurveiDampak $surveiDampak): bool
    {
        return $this->adalahPengelola($user);
    }

}
