<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use App\Policies\Concerns\BantuanOtorisasi;

/**
 * Policy otorisasi untuk model Proposal (Proposal Penelitian/PkM).
 *
 * Fase Proses (Proposal): dosen pengusul mengelola proposal miliknya
 * sendiri selama masih berstatus draft; setelah diajukan, hanya
 * pengelola LPPM yang dapat mengubah/menghapusnya (misalnya untuk
 * memproses review). Peran pemantau (prodi_gkm/pimpinan) dan reviewer
 * memiliki akses baca penuh untuk keperluan monitoring & evaluasi.
 */
class ProposalPolicy
{
    use BantuanOtorisasi;

    /**
     * Semua pengguna terautentikasi boleh melihat daftar proposal
     * (dosen hanya akan melihat detail miliknya sendiri saat membuka
     * satu proposal, lihat method view()).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Pengusul, pengelola LPPM, reviewer, dan pemantau boleh melihat
     * detail satu proposal.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $this->adalahPengelola($user)
            || $this->adalahPemantau($user)
            || $user->memilikiPeran(User::PERAN_REVIEWER)
            || $this->adalahPemilikDosen($user, $proposal);
    }

    /**
     * Dosen boleh mengajukan proposal baru; pengelola LPPM juga dapat
     * menambahkan proposal atas nama dosen jika diperlukan.
     */
    public function create(User $user): bool
    {
        return $user->memilikiPeran(User::PERAN_DOSEN) || $this->adalahPengelola($user);
    }

    /**
     * Pengusul hanya dapat mengubah proposal miliknya selama masih
     * berstatus draft. Pengelola LPPM dapat mengubah proposal kapan pun
     * (misalnya untuk memproses transisi status review).
     */
    public function update(User $user, Proposal $proposal): bool
    {
        if ($this->adalahPengelola($user)) {
            return true;
        }

        return $this->adalahPemilikDosen($user, $proposal) && $proposal->status === 'draft';
    }

    /**
     * Pengusul hanya dapat menghapus proposal miliknya selama masih
     * berstatus draft. Pengelola LPPM dapat menghapus kapan pun.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        if ($this->adalahPengelola($user)) {
            return true;
        }

        return $this->adalahPemilikDosen($user, $proposal) && $proposal->status === 'draft';
    }

}
