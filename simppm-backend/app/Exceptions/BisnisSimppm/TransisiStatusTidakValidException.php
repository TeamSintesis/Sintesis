<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika Proposal dicoba dipindahkan ke status yang tidak sah
 * dari status saat ini (misalnya dari 'draft' langsung ke 'disetujui',
 * melompati tahap 'diajukan' dan 'direview').
 */
class TransisiStatusTidakValidException extends AturanBisnisException
{
    public static function untuk(string $statusSaatIni, string $statusTujuan): self
    {
        return new self(
            "Transisi status proposal dari '{$statusSaatIni}' ke '{$statusTujuan}' tidak diperbolehkan."
        );
    }

    public function kodeError(): string
    {
        return 'TRANSISI_STATUS_TIDAK_VALID';
    }
}
