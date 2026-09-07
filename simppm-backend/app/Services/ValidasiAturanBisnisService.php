<?php

namespace App\Services;

use App\Exceptions\BisnisSimppm\KlirensEtikDiperlukanException;
use App\Exceptions\BisnisSimppm\LogbookBelumTersediaException;
use App\Exceptions\BisnisSimppm\PenilaianBelumMemenuhiAmbangBatasException;
use App\Exceptions\BisnisSimppm\PencairanMelebihiKontrakException;
use App\Exceptions\BisnisSimppm\RealisasiMelebihiKontrakException;
use App\Exceptions\BisnisSimppm\TransisiStatusTidakValidException;
use App\Models\Kontrak;
use App\Models\Monev;
use App\Models\PencairanDana;
use App\Models\Proposal;
use App\Models\Spj;

/**
 * Kumpulan aturan bisnis inti (business rules) SIMPPM yang tidak dapat
 * diwakili oleh validasi kolom biasa (mis. required/max/enum), karena
 * melibatkan hubungan lintas-entitas dan/atau agregasi data.
 *
 * Dipusatkan sebagai satu service class (bukan tersebar di banyak model)
 * agar mudah diuji secara terisolasi (unit test) tanpa perlu HTTP request
 * penuh, sekaligus memudahkan penelusuran seluruh aturan bisnis dari satu
 * tempat.
 */
class ValidasiAturanBisnisService
{
    /**
     * Graf transisi status Proposal yang sah. Kunci = status asal,
     * nilai = daftar status tujuan yang diperbolehkan.
     *
     * @var array<string, array<int, string>>
     */
    private const TRANSISI_STATUS_VALID = [
        'draft' => ['diajukan'],
        'diajukan' => ['direview', 'ditolak'],
        'direview' => ['disetujui', 'ditolak'],
        'disetujui' => [],
        'ditolak' => [],
    ];

    /**
     * Ambang batas rata-rata skor Penilaian minimum agar Proposal dapat
     * disetujui. Nilai ini idealnya berasal dari konfigurasi (config),
     * bukan angka tetap (hard-coded) dalam kode -- lihat
     * config/simppm.php sesuai prinsip 12-factor app (config terpisah
     * dari kode).
     */
    private function ambangBatasSkor(): float
    {
        return (float) config('simppm.ambang_batas_skor_penilaian', 70.0);
    }

    /**
     * Validasi 1: Transisi status Proposal.
     *
     * Memastikan Proposal hanya dapat berpindah status mengikuti alur
     * baku: draft -> diajukan -> direview -> (disetujui | ditolak).
     * Tidak boleh melompati tahap atau kembali ke tahap sebelumnya.
     *
     * @throws TransisiStatusTidakValidException
     */
    public function validasiTransisiStatusProposal(string $statusSaatIni, string $statusBaru): void
    {
        if ($statusSaatIni === $statusBaru) {
            return; // Tidak ada perubahan status, selalu diperbolehkan.
        }

        $tujuanValid = self::TRANSISI_STATUS_VALID[$statusSaatIni] ?? [];

        if (! in_array($statusBaru, $tujuanValid, true)) {
            throw TransisiStatusTidakValidException::untuk($statusSaatIni, $statusBaru);
        }
    }

    /**
     * Validasi 2: Klirens etik wajib ada sebelum Proposal direview/
     * disetujui, jika proposal ditandai berisiko etik.
     *
     * @throws KlirensEtikDiperlukanException
     */
    public function validasiKlirensEtikSebelumProses(Proposal $proposal, string $statusTujuan): void
    {
        if (! $proposal->berisiko_etik) {
            return; // Proposal tanpa risiko etik tidak memerlukan klirens.
        }

        if (! in_array($statusTujuan, ['direview', 'disetujui'], true)) {
            return; // Aturan ini hanya berlaku saat memasuki tahap review/persetujuan.
        }

        $klirensDisetujui = $proposal->klirensEtikList()
            ->where('status', 'disetujui')
            ->exists();

        if (! $klirensDisetujui) {
            throw KlirensEtikDiperlukanException::untuk($proposal->id);
        }
    }

    /**
     * Validasi 3: Monev hanya dapat diselesaikan (status = 'selesai')
     * jika Proposal terkait sudah memiliki minimal satu entri Logbook.
     *
     * @throws LogbookBelumTersediaException
     */
    public function validasiLogbookSebelumMonevSelesai(Monev $monev, string $statusBaru): void
    {
        if ($statusBaru !== 'selesai') {
            return;
        }

        $adaLogbook = $monev->proposal()
            ->first()
            ?->logbookList()
            ->exists() ?? false;

        if (! $adaLogbook) {
            throw LogbookBelumTersediaException::untuk($monev->proposal_id);
        }
    }

    /**
     * Validasi 4: Proposal hanya dapat berstatus 'disetujui' jika sudah
     * memiliki minimal satu Penilaian yang difinalisasi (status_finalisasi
     * = true) dan rata-rata skor Penilaian yang difinalisasi mencapai
     * ambang batas kelulusan.
     *
     * @throws PenilaianBelumMemenuhiAmbangBatasException
     */
    public function validasiPenilaianSebelumPersetujuan(Proposal $proposal): void
    {
        $penilaianFinal = $proposal->penilaianList()
            ->where('status_finalisasi', true)
            ->get();

        if ($penilaianFinal->isEmpty()) {
            throw PenilaianBelumMemenuhiAmbangBatasException::belumFinal($proposal->id);
        }

        $rataRata = (float) $penilaianFinal->avg('skor');
        $ambangBatas = $this->ambangBatasSkor();

        if ($rataRata < $ambangBatas) {
            throw PenilaianBelumMemenuhiAmbangBatasException::dibawahAmbangBatas(
                $proposal->id,
                round($rataRata, 2),
                $ambangBatas
            );
        }
    }

    /**
     * Validasi 5: Jumlah realisasi pada SPJ tidak boleh melebihi nilai
     * kontrak (ditelusuri melalui LaporanAkhir -> Proposal -> Kontrak).
     *
     * @throws RealisasiMelebihiKontrakException
     */
    public function validasiRealisasiSpjTidakMelebihiKontrak(Spj $spj, float $jumlahRealisasiBaru): void
    {
        $laporanAkhir = $spj->laporanAkhir()->first();
        $kontrak = $laporanAkhir?->proposal()->first()?->kontrakList()->first();

        if (! $kontrak) {
            return; // Tidak ada kontrak terkait, tidak ada yang perlu dibandingkan.
        }

        if ($jumlahRealisasiBaru > (float) $kontrak->nilai_kontrak) {
            throw RealisasiMelebihiKontrakException::untuk(
                $jumlahRealisasiBaru,
                (float) $kontrak->nilai_kontrak
            );
        }
    }

    /**
     * Validasi 6: Akumulasi seluruh PencairanDana pada satu Kontrak
     * (termasuk pencairan baru yang sedang disimpan) tidak boleh melebihi
     * nilai_kontrak.
     *
     * @throws PencairanMelebihiKontrakException
     */
    public function validasiPencairanTidakMelebihiKontrak(Kontrak $kontrak, float $jumlahBaru, ?int $pencairanIdYangDiabaikan = null): void
    {
        $query = $kontrak->pencairanDanaList()->where('id', '!=', $pencairanIdYangDiabaikan ?? 0);
        $totalSebelumnya = (float) $query->sum('jumlah');
        $totalSetelahnya = $totalSebelumnya + $jumlahBaru;

        if ($totalSetelahnya > (float) $kontrak->nilai_kontrak) {
            throw PencairanMelebihiKontrakException::untuk(
                $totalSetelahnya,
                (float) $kontrak->nilai_kontrak
            );
        }
    }
}
