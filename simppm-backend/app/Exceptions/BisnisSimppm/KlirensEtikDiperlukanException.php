<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika Proposal yang ditandai berisiko etik (berisiko_etik =
 * true) dicoba diajukan untuk direview/disetujui tanpa memiliki data
 * Klirens Etik yang berstatus 'disetujui' terlebih dahulu.
 */
class KlirensEtikDiperlukanException extends AturanBisnisException
{
    public static function untuk(int $proposalId): self
    {
        return new self(
            "Proposal #{$proposalId} berisiko etik dan wajib memiliki klirens etik berstatus 'disetujui' sebelum dapat diproses lebih lanjut."
        );
    }

    public function kodeError(): string
    {
        return 'KLIRENS_ETIK_DIPERLUKAN';
    }
}
