<?php

namespace App\Observers;

use App\Models\Spj;
use App\Services\ValidasiAturanBisnisService;

/**
 * Observer yang menegakkan aturan: jumlah_realisasi pada SPJ tidak boleh
 * melebihi nilai_kontrak pada Kontrak terkait.
 */
class SpjObserver
{
    public function __construct(
        private readonly ValidasiAturanBisnisService $validator,
    ) {
    }

    public function saving(Spj $spj): void
    {
        if (! $spj->isDirty('jumlah_realisasi')) {
            return;
        }

        $this->validator->validasiRealisasiSpjTidakMelebihiKontrak($spj, (float) $spj->jumlah_realisasi);
    }
}
