<?php

namespace App\Policies;

use App\Models\Rekognisi;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Rekognisi (Rekognisi).
 *
 * Fase Dampak (Rekognisi): dikelola oleh dosen pemilik data secara
 * langsung serta oleh pengelola LPPM. Peran pemantau memiliki akses
 * baca untuk keperluan evaluasi dampak jangka panjang.
 */
class RekognisiPolicy
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
    public function view(User $user, Rekognisi $rekognisi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $rekognisi);
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
    public function update(User $user, Rekognisi $rekognisi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $rekognisi);
    }

    /**
     * Dosen pengusul proposal terkait atau pengelola LPPM boleh menghapus.
     */
    public function delete(User $user, Rekognisi $rekognisi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $rekognisi);
    }

}
