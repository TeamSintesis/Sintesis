<?php

namespace App\Policies;

use App\Models\ProdukAdopsi;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model ProdukAdopsi (Produk/Purwarupa Adopsi).
 *
 * Fase Luaran (Produk Diadopsi Mitra): dikelola oleh dosen pengusul
 * proposal terkait ATAU mitra eksternal penerima produk, serta oleh
 * pengelola LPPM. Peran pemantau memiliki akses baca.
 */
class ProdukAdopsiPolicy
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
     * Dosen pengusul, mitra penerima, pengelola LPPM, dan pemantau boleh
     * melihat detail data.
     */
    public function view(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahPemilikDosen($user, $produkAdopsi)
            || $this->adalahPemilikMitra($user, $produkAdopsi);
    }

    /**
     * Dosen, mitra eksternal, atau pengelola LPPM boleh menambah data.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN, User::PERAN_MITRA_EKSTERNAL)
            || $this->adalahPengelola($user);
    }

    /**
     * Dosen pengusul, mitra penerima, atau pengelola LPPM boleh mengubah.
     */
    public function update(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemilikDosen($user, $produkAdopsi)
            || $this->adalahPemilikMitra($user, $produkAdopsi);
    }

    /**
     * Hanya dosen pengusul atau pengelola LPPM yang boleh menghapus.
     */
    public function delete(User $user, ProdukAdopsi $produkAdopsi): bool
    {
        return $this->adalahPengelola($user) || $this->adalahPemilikDosen($user, $produkAdopsi);
    }

}
