<?php

namespace App\Policies;

use App\Models\Monev;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Monev (Monitoring dan Evaluasi).
 *
 * Fase Proses (Monitoring dan Evaluasi): dikelola oleh reviewer yang ditugaskan
 * (kolom dosen_id merepresentasikan reviewer, bukan pengusul
 * proposal) serta oleh pengelola LPPM. Dosen pengusul proposal
 * terkait dan peran pemantau hanya memiliki akses baca.
 */
class MonevPolicy
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
     * Reviewer yang ditugaskan, dosen pengusul proposal terkait,
     * pengelola LPPM, dan pemantau boleh melihat detail data.
     */
    public function view(User $user, Monev $monev): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $this->adalahReviewerYangDitugaskan($user, $monev)
            || $this->adalahPemilikDosen($user, $monev);
    }

    /**
     * Reviewer atau pengelola LPPM boleh menambah data penilaian/monev.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_REVIEWER) || $this->adalahPengelola($user);
    }

    /**
     * Hanya reviewer yang ditugaskan pada data tersebut atau pengelola
     * LPPM yang boleh mengubahnya.
     */
    public function update(User $user, Monev $monev): bool
    {
        return $this->adalahPengelola($user) || $this->adalahReviewerYangDitugaskan($user, $monev);
    }

    /**
     * Hanya pengelola LPPM yang boleh menghapus data penilaian/monev.
     */
    public function delete(User $user, Monev $monev): bool
    {
        return $this->adalahPengelola($user);
    }

}
