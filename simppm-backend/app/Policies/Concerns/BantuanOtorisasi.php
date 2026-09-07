<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Kumpulan method bantu (helper) yang dipakai bersama oleh seluruh kelas
 * Policy SIMPPM untuk memeriksa peran (role) dan kepemilikan data.
 *
 * Menggunakan trait ini menghindari duplikasi logika otorisasi yang sama
 * di 27 kelas Policy, sekaligus menjaga konsistensi aturan di seluruh
 * modul (prinsip DRY / best practice pengembangan aplikasi web).
 */
trait BantuanOtorisasi
{
    /**
     * Apakah pengguna adalah pengelola LPPM (admin_lppm atau ketua_lppm)?
     *
     * Pengelola LPPM selalu diberi akses penuh (CRUD) ke seluruh data
     * SIMPPM, sesuai perannya sebagai pengelola tata kelola PPM.
     */
    protected function adalahPengelola(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_ADMIN_LPPM, User::PERAN_KETUA_LPPM);
    }

    /**
     * Apakah pengguna berperan sebagai pengawas/pemantau saja (prodi/GKM
     * atau pimpinan)? Peran ini hanya diberi akses baca (read-only) untuk
     * keperluan monitoring dan evaluasi internal.
     */
    protected function adalahPemantau(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_PRODI_GKM, User::PERAN_PIMPINAN);
    }

    /**
     * Apakah pengguna adalah dosen pemilik/pengusul data $model?
     *
     * Model target harus menyediakan method pemilikDosenId() yang
     * menelusuri relasi (langsung atau berjenjang) hingga menemukan
     * dosen pengusul.
     */
    protected function adalahPemilikDosen(User $user, object $model): bool
    {
        if (! $user->memilikiPeran(User::PERAN_DOSEN) || ! $user->dosen_id) {
            return false;
        }

        if (! method_exists($model, 'pemilikDosenId')) {
            return false;
        }

        return $model->pemilikDosenId() === $user->dosen_id;
    }

    /**
     * Apakah pengguna adalah mitra eksternal pemilik data $model?
     *
     * Model target harus menyediakan method pemilikMitraId().
     */
    protected function adalahPemilikMitra(User $user, object $model): bool
    {
        if (! $user->memilikiPeran(User::PERAN_MITRA_EKSTERNAL) || ! $user->mitra_id) {
            return false;
        }

        if (! method_exists($model, 'pemilikMitraId')) {
            return false;
        }

        return $model->pemilikMitraId() === $user->mitra_id;
    }

    /**
     * Apakah pengguna adalah reviewer yang ditugaskan pada data $model
     * (dipakai untuk Penilaian & Monev, yang kolom dosen_id-nya berarti
     * reviewer yang ditugaskan, bukan pengusul proposal)?
     */
    protected function adalahReviewerYangDitugaskan(User $user, object $model): bool
    {
        return $user->memilikiPeran(User::PERAN_REVIEWER)
            && $user->dosen_id
            && isset($model->dosen_id)
            && $model->dosen_id === $user->dosen_id;
    }
}
