<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika Monev dicoba diselesaikan (status = 'selesai') padahal
 * proposal terkait belum memiliki minimal satu entri Logbook, sebagai
 * bukti kemajuan pelaksanaan penelitian/PkM.
 */
class LogbookBelumTersediaException extends AturanBisnisException
{
    public static function untuk(int $proposalId): self
    {
        return new self(
            "Monev tidak dapat diselesaikan karena Proposal #{$proposalId} belum memiliki satu pun entri Logbook."
        );
    }

    public function kodeError(): string
    {
        return 'LOGBOOK_BELUM_TERSEDIA';
    }
}
