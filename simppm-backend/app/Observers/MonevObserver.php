<?php

namespace App\Observers;

use App\Models\Monev;
use App\Services\ValidasiAturanBisnisService;

/**
 * Observer yang menegakkan aturan: Monev hanya dapat diselesaikan jika
 * Proposal terkait sudah memiliki minimal satu entri Logbook.
 */
class MonevObserver
{
    public function __construct(
        private readonly ValidasiAturanBisnisService $validator,
    ) {
    }

    public function saving(Monev $monev): void
    {
        if (! $monev->isDirty('status')) {
            return;
        }

        $this->validator->validasiLogbookSebelumMonevSelesai($monev, $monev->status);
    }
}
