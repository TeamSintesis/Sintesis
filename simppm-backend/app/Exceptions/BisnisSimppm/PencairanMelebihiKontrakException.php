<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika akumulasi jumlah seluruh PencairanDana pada satu
 * Kontrak (termasuk pencairan baru yang sedang disimpan) melebihi
 * nilai_kontrak pada Kontrak tersebut.
 */
class PencairanMelebihiKontrakException extends AturanBisnisException
{
    public static function untuk(float $totalSetelahPencairan, float $nilaiKontrak): self
    {
        return new self(
            "Total pencairan dana ({$totalSetelahPencairan}) akan melebihi nilai kontrak ({$nilaiKontrak})."
        );
    }

    public function kodeError(): string
    {
        return 'PENCAIRAN_MELEBIHI_KONTRAK';
    }
}
