<?php

namespace App\Observers;

use App\Models\PencairanDana;
use App\Services\ValidasiAturanBisnisService;

/**
 * Observer yang menegakkan aturan: akumulasi seluruh PencairanDana pada
 * satu Kontrak (termasuk pencairan yang sedang disimpan) tidak boleh
 * melebihi nilai_kontrak.
 */
class PencairanDanaObserver
{
    public function __construct(
        private readonly ValidasiAturanBisnisService $validator,
    ) {
    }

    public function saving(PencairanDana $pencairanDana): void
    {
        if (! $pencairanDana->isDirty('jumlah')) {
            return;
        }

        $kontrak = $pencairanDana->kontrak()->first();

        if (! $kontrak) {
            return;
        }

        $this->validator->validasiPencairanTidakMelebihiKontrak(
            $kontrak,
            (float) $pencairanDana->jumlah,
            $pencairanDana->exists ? $pencairanDana->id : null
        );
    }
}
