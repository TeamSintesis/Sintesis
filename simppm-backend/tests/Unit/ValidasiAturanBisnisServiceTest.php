<?php

namespace Tests\Unit;

use App\Exceptions\BisnisSimppm\KlirensEtikDiperlukanException;
use App\Exceptions\BisnisSimppm\LogbookBelumTersediaException;
use App\Exceptions\BisnisSimppm\PencairanMelebihiKontrakException;
use App\Exceptions\BisnisSimppm\PenilaianBelumMemenuhiAmbangBatasException;
use App\Exceptions\BisnisSimppm\RealisasiMelebihiKontrakException;
use App\Exceptions\BisnisSimppm\TransisiStatusTidakValidException;
use App\Models\Kontrak;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\Monev;
use App\Models\PencairanDana;
use App\Models\Penilaian;
use App\Models\Proposal;
use App\Models\Spj;
use App\Services\ValidasiAturanBisnisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Test untuk ValidasiAturanBisnisService, yaitu 6 aturan bisnis
 * inti SIMPPM yang tidak dapat diwakili oleh validasi kolom biasa.
 *
 * Setiap aturan diuji dalam dua lapis:
 * 1. Memanggil method service secara langsung (unit murni), untuk
 *    memastikan logika ambang batas/kondisi sudah benar.
 * 2. Menyimpan model terkait melalui Eloquent (memicu Observer), untuk
 *    memastikan aturan benar-benar ditegakkan di jalur penyimpanan data
 *    yang sesungguhnya (API, tinker, seeder, dll), bukan hanya dapat
 *    dipanggil manual.
 *
 * Menggunakan RefreshDatabase (SQLite in-memory) karena beberapa aturan
 * melibatkan query relasi lintas-entitas (mis. menghitung akumulasi
 * PencairanDana pada satu Kontrak).
 */
class ValidasiAturanBisnisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ValidasiAturanBisnisService $validator;

    protected function setUp(): void
    {
        parent::setUp();

        // Service ini tidak memiliki dependensi konstruktor (stateless),
        // sehingga aman diinstansiasi langsung tanpa container.
        $this->validator = new ValidasiAturanBisnisService();
    }

    // -----------------------------------------------------------------
    // Aturan 1: Transisi status Proposal
    // -----------------------------------------------------------------

    /**
     * Seluruh transisi pada alur baku draft -> diajukan -> direview ->
     * (disetujui | ditolak) harus diperbolehkan.
     */
    public function test_transisi_status_valid_tidak_melempar_exception(): void
    {
        $transisiValid = [
            ['draft', 'diajukan'],
            ['diajukan', 'direview'],
            ['diajukan', 'ditolak'],
            ['direview', 'disetujui'],
            ['direview', 'ditolak'],
        ];

        foreach ($transisiValid as [$dari, $ke]) {
            $this->validator->validasiTransisiStatusProposal($dari, $ke);
        }

        // Tidak ada exception yang dilempar untuk seluruh transisi di atas.
        $this->assertTrue(true);
    }

    /**
     * Status yang tidak berubah (dari == ke) selalu diperbolehkan, karena
     * tidak dianggap sebagai transisi.
     */
    public function test_status_tidak_berubah_selalu_diperbolehkan(): void
    {
        $this->validator->validasiTransisiStatusProposal('direview', 'direview');
        $this->assertTrue(true);
    }

    /**
     * Melompati tahap (mis. draft langsung ke disetujui) harus ditolak.
     */
    public function test_transisi_status_melompat_tahap_dilempar_exception(): void
    {
        $this->expectException(TransisiStatusTidakValidException::class);

        $this->validator->validasiTransisiStatusProposal('draft', 'disetujui');
    }

    /**
     * Status akhir ('disetujui'/'ditolak') tidak dapat bertransisi lagi
     * ke status apa pun.
     */
    public function test_transisi_dari_status_akhir_dilempar_exception(): void
    {
        $this->expectException(TransisiStatusTidakValidException::class);

        $this->validator->validasiTransisiStatusProposal('disetujui', 'diajukan');
    }

    /**
     * Kode error mesin (machine-readable) harus konsisten agar frontend
     * dapat menampilkan pesan yang sesuai tanpa mem-parsing teks bebas.
     */
    public function test_kode_error_transisi_status_sesuai(): void
    {
        try {
            $this->validator->validasiTransisiStatusProposal('draft', 'ditolak');
            $this->fail('Seharusnya melempar TransisiStatusTidakValidException.');
        } catch (TransisiStatusTidakValidException $e) {
            $this->assertSame('TRANSISI_STATUS_TIDAK_VALID', $e->kodeError());
        }
    }

    /**
     * Integrasi Observer: pembuatan Proposal baru (create) dengan status
     * awal apa pun tidak boleh diblokir oleh validasi transisi, karena
     * belum ada status "asal" yang sesungguhnya.
     */
    public function test_observer_membuat_proposal_baru_tidak_diblokir_validasi_transisi(): void
    {
        $proposal = Proposal::factory()->direview()->create();

        $this->assertSame('direview', $proposal->fresh()->status);
    }

    /**
     * Integrasi Observer: memperbarui Proposal yang sudah ada dengan
     * melompati tahap harus ditolak oleh Observer.
     */
    public function test_observer_menolak_update_proposal_melompat_tahap(): void
    {
        $proposal = Proposal::factory()->create(); // status: draft

        $this->expectException(TransisiStatusTidakValidException::class);

        $proposal->update(['status' => 'disetujui']);
    }

    /**
     * Integrasi Observer: memperbarui Proposal mengikuti alur baku harus
     * diperbolehkan.
     */
    public function test_observer_mengizinkan_update_proposal_mengikuti_alur_baku(): void
    {
        $proposal = Proposal::factory()->create(); // status: draft

        $proposal->update(['status' => 'diajukan']);
        $proposal->update(['status' => 'direview']);
        $proposal->update(['status' => 'ditolak']);

        $this->assertSame('ditolak', $proposal->fresh()->status);
    }

    // -----------------------------------------------------------------
    // Aturan 2: Klirens etik wajib sebelum proposal berisiko etik
    // diproses (direview/disetujui)
    // -----------------------------------------------------------------

    /**
     * Proposal yang tidak berisiko etik tidak pernah memerlukan klirens
     * etik, apa pun status tujuannya.
     */
    public function test_klirens_etik_tidak_diperlukan_jika_proposal_tidak_berisiko(): void
    {
        $proposal = Proposal::factory()->create(['berisiko_etik' => false]);

        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'direview');
        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'disetujui');

        $this->assertTrue(true);
    }

    /**
     * Aturan ini hanya berlaku saat status tujuan direview/disetujui;
     * transisi ke status lain (mis. 'diajukan') tidak memicu pengecekan
     * meskipun proposal berisiko etik.
     */
    public function test_klirens_etik_tidak_diperiksa_untuk_status_tujuan_selain_review_atau_setuju(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();

        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'diajukan');

        $this->assertTrue(true);
    }

    /**
     * Proposal berisiko etik yang akan direview tanpa klirens etik
     * berstatus 'disetujui' harus ditolak.
     */
    public function test_klirens_etik_diperlukan_jika_belum_ada_yang_disetujui(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();

        $this->expectException(KlirensEtikDiperlukanException::class);

        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'direview');
    }

    /**
     * Proposal berisiko etik yang sudah memiliki klirens etik berstatus
     * 'disetujui' boleh melanjutkan ke tahap review/persetujuan.
     */
    public function test_klirens_etik_terpenuhi_jika_sudah_ada_yang_disetujui(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();
        $proposal->klirensEtikList()->create([
            'status' => 'disetujui',
            'nomor_sertifikat' => 'KE/2026/001',
            'tanggal_terbit' => '2026-01-10',
        ]);

        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'direview');

        $this->assertTrue(true);
    }

    /**
     * Klirens etik yang belum disetujui (mis. masih 'diajukan') tidak
     * dianggap memenuhi syarat.
     */
    public function test_klirens_etik_yang_belum_disetujui_tidak_memenuhi_syarat(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();
        $proposal->klirensEtikList()->create([
            'status' => 'diajukan',
            'nomor_sertifikat' => 'KE/2026/002',
            'tanggal_terbit' => '2026-01-10',
        ]);

        $this->expectException(KlirensEtikDiperlukanException::class);

        $this->validator->validasiKlirensEtikSebelumProses($proposal, 'direview');
    }

    /**
     * Integrasi Observer: Proposal berisiko etik yang dipindahkan ke
     * 'direview' tanpa klirens etik disetujui harus ditolak melalui
     * jalur penyimpanan Eloquent yang sesungguhnya.
     */
    public function test_observer_menolak_proposal_berisiko_etik_direview_tanpa_klirens(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();
        $proposal->update(['status' => 'diajukan']);

        $this->expectException(KlirensEtikDiperlukanException::class);

        $proposal->update(['status' => 'direview']);
    }

    /**
     * Integrasi Observer: dengan klirens etik disetujui, Proposal
     * berisiko etik dapat melanjutkan ke 'direview'.
     */
    public function test_observer_mengizinkan_proposal_berisiko_etik_direview_dengan_klirens(): void
    {
        $proposal = Proposal::factory()->berisikoEtik()->create();
        $proposal->klirensEtikList()->create([
            'status' => 'disetujui',
            'nomor_sertifikat' => 'KE/2026/003',
            'tanggal_terbit' => '2026-01-10',
        ]);
        $proposal->update(['status' => 'diajukan']);

        $proposal->update(['status' => 'direview']);

        $this->assertSame('direview', $proposal->fresh()->status);
    }

    // -----------------------------------------------------------------
    // Aturan 3: Monev hanya dapat diselesaikan jika Proposal terkait
    // sudah memiliki minimal satu entri Logbook
    // -----------------------------------------------------------------

    /**
     * Monev yang belum berstatus 'selesai' tidak memeriksa keberadaan
     * Logbook.
     */
    public function test_logbook_tidak_diperiksa_jika_monev_belum_selesai(): void
    {
        $monev = Monev::factory()->create(['status' => 'terjadwal']);

        $this->validator->validasiLogbookSebelumMonevSelesai($monev, 'terjadwal');

        $this->assertTrue(true);
    }

    /**
     * Monev tidak dapat diselesaikan jika Proposal terkait belum
     * memiliki satu pun entri Logbook.
     */
    public function test_monev_tidak_dapat_selesai_tanpa_logbook(): void
    {
        $monev = Monev::factory()->create(['status' => 'terjadwal']);

        $this->expectException(LogbookBelumTersediaException::class);

        $this->validator->validasiLogbookSebelumMonevSelesai($monev, 'selesai');
    }

    /**
     * Monev dapat diselesaikan jika Proposal terkait sudah memiliki
     * minimal satu entri Logbook.
     */
    public function test_monev_dapat_selesai_dengan_logbook(): void
    {
        $monev = Monev::factory()->create(['status' => 'terjadwal']);
        Logbook::factory()->create(['proposal_id' => $monev->proposal_id]);

        $this->validator->validasiLogbookSebelumMonevSelesai($monev, 'selesai');

        $this->assertTrue(true);
    }

    /**
     * Integrasi Observer: memperbarui Monev menjadi 'selesai' tanpa
     * Logbook harus ditolak melalui jalur penyimpanan Eloquent.
     */
    public function test_observer_menolak_monev_selesai_tanpa_logbook(): void
    {
        $monev = Monev::factory()->create(['status' => 'terjadwal']);

        $this->expectException(LogbookBelumTersediaException::class);

        $monev->update(['status' => 'selesai']);
    }

    /**
     * Integrasi Observer: memperbarui Monev menjadi 'selesai' dengan
     * Logbook yang tersedia harus diperbolehkan.
     */
    public function test_observer_mengizinkan_monev_selesai_dengan_logbook(): void
    {
        $monev = Monev::factory()->create(['status' => 'terjadwal']);
        Logbook::factory()->create(['proposal_id' => $monev->proposal_id]);

        $monev->update(['status' => 'selesai']);

        $this->assertSame('selesai', $monev->fresh()->status);
    }

    // -----------------------------------------------------------------
    // Aturan 4: Proposal hanya dapat disetujui jika sudah memiliki
    // Penilaian yang difinalisasi dengan rata-rata skor mencapai ambang
    // batas
    // -----------------------------------------------------------------

    /**
     * Proposal tanpa satu pun Penilaian yang difinalisasi tidak dapat
     * disetujui.
     */
    public function test_persetujuan_ditolak_jika_belum_ada_penilaian_final(): void
    {
        $proposal = Proposal::factory()->create();

        $this->expectException(PenilaianBelumMemenuhiAmbangBatasException::class);

        $this->validator->validasiPenilaianSebelumPersetujuan($proposal);
    }

    /**
     * Penilaian yang belum difinalisasi (status_finalisasi = false)
     * tidak dihitung, sehingga persetujuan tetap ditolak.
     */
    public function test_penilaian_belum_difinalisasi_tidak_dihitung(): void
    {
        $proposal = Proposal::factory()->create();
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '95.00',
            'status_finalisasi' => false,
        ]);

        $this->expectException(PenilaianBelumMemenuhiAmbangBatasException::class);

        $this->validator->validasiPenilaianSebelumPersetujuan($proposal);
    }

    /**
     * Rata-rata skor Penilaian yang difinalisasi di bawah ambang batas
     * (default 70.0) harus ditolak dengan pesan yang menyertakan
     * rata-rata dan ambang batas.
     */
    public function test_persetujuan_ditolak_jika_rata_rata_skor_di_bawah_ambang_batas(): void
    {
        $proposal = Proposal::factory()->create();
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '50.00',
            'status_finalisasi' => true,
        ]);

        try {
            $this->validator->validasiPenilaianSebelumPersetujuan($proposal);
            $this->fail('Seharusnya melempar PenilaianBelumMemenuhiAmbangBatasException.');
        } catch (PenilaianBelumMemenuhiAmbangBatasException $e) {
            $this->assertSame('PENILAIAN_BELUM_MEMENUHI_AMBANG_BATAS', $e->kodeError());
            $this->assertStringContainsString('50', $e->getMessage());
        }
    }

    /**
     * Rata-rata skor Penilaian yang difinalisasi mencapai/di atas ambang
     * batas harus diperbolehkan.
     */
    public function test_persetujuan_diperbolehkan_jika_rata_rata_skor_mencapai_ambang_batas(): void
    {
        config(['simppm.ambang_batas_skor_penilaian' => 70.0]);

        $proposal = Proposal::factory()->create();
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '80.00',
            'status_finalisasi' => true,
        ]);
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '90.00',
            'status_finalisasi' => true,
        ]);

        $this->validator->validasiPenilaianSebelumPersetujuan($proposal);

        $this->assertTrue(true);
    }

    /**
     * Ambang batas skor dapat dikonfigurasi lewat env/config (sesuai
     * prinsip 12-factor app: config terpisah dari kode), bukan angka
     * tetap dalam kode.
     */
    public function test_ambang_batas_skor_dapat_dikonfigurasi(): void
    {
        config(['simppm.ambang_batas_skor_penilaian' => 95.0]);

        $proposal = Proposal::factory()->create();
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '90.00',
            'status_finalisasi' => true,
        ]);

        // Skor 90 lolos ambang batas bawaan (70) tetapi tidak lolos
        // ambang batas kustom (95) yang baru saja diset.
        $this->expectException(PenilaianBelumMemenuhiAmbangBatasException::class);

        $this->validator->validasiPenilaianSebelumPersetujuan($proposal);
    }

    /**
     * Integrasi Observer: Proposal yang dipindahkan ke 'disetujui' tanpa
     * Penilaian final harus ditolak melalui jalur penyimpanan Eloquent.
     */
    public function test_observer_menolak_persetujuan_proposal_tanpa_penilaian_final(): void
    {
        $proposal = Proposal::factory()->create();
        $proposal->update(['status' => 'diajukan']);
        $proposal->update(['status' => 'direview']);

        $this->expectException(PenilaianBelumMemenuhiAmbangBatasException::class);

        $proposal->update(['status' => 'disetujui']);
    }

    /**
     * Integrasi Observer: Proposal dengan Penilaian final di atas ambang
     * batas dapat disetujui melalui jalur penyimpanan Eloquent.
     */
    public function test_observer_mengizinkan_persetujuan_proposal_dengan_penilaian_memadai(): void
    {
        config(['simppm.ambang_batas_skor_penilaian' => 70.0]);

        $proposal = Proposal::factory()->create();
        Penilaian::factory()->create([
            'proposal_id' => $proposal->id,
            'skor' => '85.00',
            'status_finalisasi' => true,
        ]);
        $proposal->update(['status' => 'diajukan']);
        $proposal->update(['status' => 'direview']);

        $proposal->update(['status' => 'disetujui']);

        $this->assertSame('disetujui', $proposal->fresh()->status);
    }

    // -----------------------------------------------------------------
    // Aturan 5: Realisasi SPJ tidak boleh melebihi nilai kontrak
    // -----------------------------------------------------------------

    /**
     * SPJ tanpa Kontrak terkait (mis. rantai relasi terputus) tidak
     * dapat dibandingkan, sehingga tidak ada yang perlu ditolak.
     */
    public function test_realisasi_spj_dilewati_jika_tidak_ada_kontrak_terkait(): void
    {
        $laporanAkhir = LaporanAkhir::factory()->create();
        $spj = Spj::factory()->create(['laporan_akhir_id' => $laporanAkhir->id]);

        // Tidak dibuat Kontrak apa pun untuk Proposal terkait.
        $this->validator->validasiRealisasiSpjTidakMelebihiKontrak($spj, 1_000_000);

        $this->assertTrue(true);
    }

    /**
     * Jumlah realisasi yang melebihi nilai kontrak harus ditolak.
     */
    public function test_realisasi_spj_melebihi_kontrak_dilempar_exception(): void
    {
        $proposal = Proposal::factory()->create();
        Kontrak::factory()->create([
            'proposal_id' => $proposal->id,
            'nilai_kontrak' => '5000000.00',
        ]);
        $laporanAkhir = LaporanAkhir::factory()->create(['proposal_id' => $proposal->id]);
        // 'jumlah_realisasi' diset kecil saat pembuatan (bukan nilai
        // acak bawaan factory) agar tidak memicu Observer pada saat
        // setup -- nilai yang akan diuji dikirim lewat pemanggilan
        // service secara langsung di bawah.
        $spj = Spj::factory()->create([
            'laporan_akhir_id' => $laporanAkhir->id,
            'jumlah_realisasi' => '1000000.00',
        ]);

        $this->expectException(RealisasiMelebihiKontrakException::class);

        $this->validator->validasiRealisasiSpjTidakMelebihiKontrak($spj, 9_000_000);
    }

    /**
     * Jumlah realisasi yang tidak melebihi nilai kontrak harus
     * diperbolehkan.
     */
    public function test_realisasi_spj_di_bawah_kontrak_diperbolehkan(): void
    {
        $proposal = Proposal::factory()->create();
        Kontrak::factory()->create([
            'proposal_id' => $proposal->id,
            'nilai_kontrak' => '5000000.00',
        ]);
        $laporanAkhir = LaporanAkhir::factory()->create(['proposal_id' => $proposal->id]);
        $spj = Spj::factory()->create([
            'laporan_akhir_id' => $laporanAkhir->id,
            'jumlah_realisasi' => '1000000.00',
        ]);

        $this->validator->validasiRealisasiSpjTidakMelebihiKontrak($spj, 4_000_000);

        $this->assertTrue(true);
    }

    /**
     * Integrasi Observer: memperbarui jumlah_realisasi SPJ melebihi
     * nilai kontrak harus ditolak melalui jalur penyimpanan Eloquent.
     */
    public function test_observer_menolak_update_realisasi_spj_melebihi_kontrak(): void
    {
        $proposal = Proposal::factory()->create();
        Kontrak::factory()->create([
            'proposal_id' => $proposal->id,
            'nilai_kontrak' => '5000000.00',
        ]);
        $laporanAkhir = LaporanAkhir::factory()->create(['proposal_id' => $proposal->id]);
        $spj = Spj::factory()->create([
            'laporan_akhir_id' => $laporanAkhir->id,
            'jumlah_realisasi' => '1000000.00',
        ]);

        $this->expectException(RealisasiMelebihiKontrakException::class);

        $spj->update(['jumlah_realisasi' => '9000000.00']);
    }

    // -----------------------------------------------------------------
    // Aturan 6: Akumulasi PencairanDana pada satu Kontrak tidak boleh
    // melebihi nilai kontrak
    // -----------------------------------------------------------------

    /**
     * Pencairan tunggal yang tidak melebihi nilai kontrak harus
     * diperbolehkan.
     */
    public function test_pencairan_tunggal_di_bawah_kontrak_diperbolehkan(): void
    {
        $kontrak = Kontrak::factory()->create(['nilai_kontrak' => '10000000.00']);

        $this->validator->validasiPencairanTidakMelebihiKontrak($kontrak, 5_000_000);

        $this->assertTrue(true);
    }

    /**
     * Akumulasi pencairan yang sudah ada ditambah pencairan baru yang
     * melebihi nilai kontrak harus ditolak.
     */
    public function test_akumulasi_pencairan_melebihi_kontrak_dilempar_exception(): void
    {
        $kontrak = Kontrak::factory()->create(['nilai_kontrak' => '10000000.00']);
        PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '6000000.00',
        ]);

        $this->expectException(PencairanMelebihiKontrakException::class);

        $this->validator->validasiPencairanTidakMelebihiKontrak($kontrak, 5_000_000);
    }

    /**
     * Saat memperbarui (bukan membuat baru) sebuah PencairanDana yang
     * sudah ada, akumulasinya sendiri harus diabaikan dari perhitungan
     * (via $pencairanIdYangDiabaikan) agar tidak dihitung dobel.
     */
    public function test_update_pencairan_dana_mengabaikan_dirinya_sendiri_dari_akumulasi(): void
    {
        $kontrak = Kontrak::factory()->create(['nilai_kontrak' => '10000000.00']);
        $pencairan = PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '6000000.00',
        ]);

        // Memperbesar jumlah pencairan yang SAMA menjadi 9.000.000 tetap
        // di bawah nilai kontrak (10.000.000) jika jumlah lama (6 juta)
        // diabaikan dari akumulasi, bukan dihitung dua kali.
        $this->validator->validasiPencairanTidakMelebihiKontrak($kontrak, 9_000_000, $pencairan->id);

        $this->assertTrue(true);
    }

    /**
     * Integrasi Observer: menambahkan PencairanDana kedua yang membuat
     * akumulasi melebihi nilai kontrak harus ditolak melalui jalur
     * penyimpanan Eloquent.
     */
    public function test_observer_menolak_pencairan_dana_kedua_yang_melebihi_kontrak(): void
    {
        $kontrak = Kontrak::factory()->create(['nilai_kontrak' => '10000000.00']);
        PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '6000000.00',
        ]);

        $this->expectException(PencairanMelebihiKontrakException::class);

        PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '5000000.00',
        ]);
    }

    /**
     * Integrasi Observer: pencairan dana dalam batas kontrak harus
     * diperbolehkan.
     */
    public function test_observer_mengizinkan_pencairan_dana_dalam_batas_kontrak(): void
    {
        $kontrak = Kontrak::factory()->create(['nilai_kontrak' => '10000000.00']);
        PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '6000000.00',
        ]);

        $pencairanKedua = PencairanDana::factory()->create([
            'kontrak_id' => $kontrak->id,
            'jumlah' => '4000000.00',
        ]);

        $this->assertSame('4000000.00', (string) $pencairanKedua->fresh()->jumlah);
    }
}
