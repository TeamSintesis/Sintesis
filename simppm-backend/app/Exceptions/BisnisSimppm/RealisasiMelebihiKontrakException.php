<?php

namespace App\Exceptions\BisnisSimppm;

/**
 * Dilempar ketika nilai jumlah_realisasi pada SPJ melebihi nilai_kontrak
 * pada Kontrak terkait (ditelusuri melalui LaporanAkhir -> Proposal ->
 * Kontrak). Pertanggungjawaban keuangan tidak boleh melampaui nilai
 * kontrak yang disepakati.
 */
class RealisasiMelebihiKontrakException extends AturanBisnisException
{
    public static function untuk(float $realisasi, float $nilaiKontrak): self
    {
        return new self(
            "Jumlah realisasi SPJ ({$realisasi}) melebihi nilai kontrak yang tersedia ({$nilaiKontrak})."
        );
    }

    public function kodeError(): string
    {
        return 'REALISASI_MELEBIHI_KONTRAK';
    }
}
