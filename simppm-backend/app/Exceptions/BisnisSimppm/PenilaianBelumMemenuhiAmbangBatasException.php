<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika Proposal dicoba disetujui ('disetujui') padahal belum
 * memiliki Penilaian yang sudah difinalisasi (status_finalisasi = true)
 * dengan rata-rata skor mencapai ambang batas minimum kelulusan.
 */
class PenilaianBelumMemenuhiAmbangBatasException extends AturanBisnisException
{
    public static function belumFinal(int $proposalId): self
    {
        return new self(
            "Proposal #{$proposalId} belum memiliki Penilaian yang difinalisasi oleh reviewer."
        );
    }

    public static function dibawahAmbangBatas(int $proposalId, float $rataRata, float $ambangBatas): self
    {
        return new self(
            "Rata-rata skor Penilaian Proposal #{$proposalId} ({$rataRata}) berada di bawah ambang batas kelulusan ({$ambangBatas})."
        );
    }

    public function kodeError(): string
    {
        return 'PENILAIAN_BELUM_MEMENUHI_AMBANG_BATAS';
    }
}
