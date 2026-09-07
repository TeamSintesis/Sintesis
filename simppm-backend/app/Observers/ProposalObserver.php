<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Services\ValidasiAturanBisnisService;

/**
 * Observer yang menegakkan aturan bisnis terkait siklus hidup Proposal:
 * validitas transisi status, keharusan klirens etik untuk proposal
 * berisiko etik, dan syarat penilaian minimum sebelum disetujui.
 *
 * Menggunakan Observer (bukan menaruh logika langsung di Controller/
 * Request) agar aturan tetap ditegakkan dari jalur mana pun data
 * dimodifikasi (API, tinker, seeder, dll), sesuai praktik terbaik
 * "fat models/services, thin controllers".
 */
class ProposalObserver
{
    public function __construct(
        private readonly ValidasiAturanBisnisService $validator,
    ) {
    }

    /**
     * Dipanggil sebelum Proposal disimpan (baik create maupun update).
     * Memvalidasi transisi status dan syarat klirens etik/penilaian
     * hanya ketika kolom status benar-benar berubah.
     */
    public function saving(Proposal $proposal): void
    {
        if (! $proposal->isDirty('status')) {
            return;
        }

        $statusSaatIni = $proposal->getOriginal('status') ?? 'draft';
        $statusBaru = $proposal->status;

        // Jika data baru (belum ada di database), anggap status asal
        // adalah 'draft' sesuai default kolom -- lewati validasi transisi
        // agar proses pembuatan Proposal dengan status awal apa pun
        // (umumnya 'draft') tidak diblokir.
        if ($proposal->exists) {
            $this->validator->validasiTransisiStatusProposal($statusSaatIni, $statusBaru);
        }

        $this->validator->validasiKlirensEtikSebelumProses($proposal, $statusBaru);

        if ($statusBaru === 'disetujui') {
            $this->validator->validasiPenilaianSebelumPersetujuan($proposal);
        }
    }
}
