# Spesifikasi Kebutuhan Fungsional (SKF) SIMPPM
### Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat

Dokumen ini merinci kebutuhan fungsional tiap modul pada model arsitektur SIMPPM (4 fase: Masukan, Proses, Luaran, Dampak) yang telah dirancang sebelumnya, sebagai acuan bagi tim pengembang dalam membangun sistem.

**Legenda Prioritas (MoSCoW):**
- **M** = Must Have (wajib ada, kebutuhan minimum kepatuhan regulasi/akreditasi)
- **S** = Should Have (penting, meningkatkan kualitas pengelolaan)
- **C** = Could Have (nilai tambah, dapat ditunda ke tahap lanjutan)

---

## FASE 1 — MASUKAN

### Modul M1: Renstra & Peta Jalan

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-M1-01 | Sistem dapat mengunggah dan menyimpan dokumen Renstra Penelitian/PkM (format PDF) beserta metadata: periode berlaku, unit penyusun, tanggal pengesahan | Admin LPPM | M |
| FR-M1-02 | Sistem dapat mencatat peta jalan penelitian/PkM per bidang keilmuan/prodi, mencakup tema unggulan, tahapan capaian per tahun, dan target luaran | Admin LPPM, Ketua LPPM | M |
| FR-M1-03 | Sistem menyediakan riwayat versi dokumen (versioning) sehingga revisi renstra/peta jalan sebelumnya tetap dapat ditelusuri | Admin LPPM | S |
| FR-M1-04 | Sistem dapat menautkan (link) tema penelitian/PkM yang diajukan dosen pada modul Proposal dengan peta jalan yang relevan, untuk mengukur kesesuaian | Sistem, Reviewer | S |
| FR-M1-05 | Sistem dapat menghasilkan laporan kesesuaian antara realisasi penelitian/PkM dengan peta jalan yang direncanakan (untuk evaluasi PPEPP) | Ketua LPPM, Pimpinan | S |
| FR-M1-06 | Sistem dapat mengekspor dokumen Renstra & Peta Jalan sebagai lampiran bukti pada borang LED | Admin LPPM, GKM/Prodi | M |

### Modul M2: Pedoman & Etik Penelitian

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-M2-01 | Sistem dapat menyimpan dan mempublikasikan pedoman penelitian, pedoman PkM, dan kode etik terbaru yang dapat diakses seluruh dosen | Admin LPPM | M |
| FR-M2-02 | Sistem otomatis menandai versi pedoman yang aktif/berlaku dan mengarsipkan versi sebelumnya | Admin LPPM | S |
| FR-M2-03 | Sistem mengirim notifikasi kepada dosen/peneliti saat ada pembaruan pedoman atau kebijakan baru | Sistem | C |
| FR-M2-04 | Sistem menyediakan riwayat unduhan pedoman untuk keperluan audit kepatuhan | Admin LPPM | C |

### Modul M3: Basis Data Peneliti/Pakar

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-M3-01 | Sistem dapat menyimpan profil dosen/peneliti: NIDN, bidang kepakaran, jabatan fungsional, riwayat pendidikan | Admin LPPM, Dosen | M |
| FR-M3-02 | Sistem dapat menautkan ID SINTA, Scopus ID, dan ORCID pada profil masing-masing dosen | Dosen, Admin LPPM | M |
| FR-M3-03 | Sistem dapat menampilkan rekam jejak (histori) penelitian dan PkM setiap dosen secara otomatis dari data proposal dan luaran yang tersimpan | Sistem | M |
| FR-M3-04 | Sistem dapat melakukan sinkronisasi/impor data kepegawaian dasar (NIDN, nama, prodi) dari sistem SDM kampus agar tidak entri ganda | Admin LPPM | S |
| FR-M3-05 | Sistem dapat digunakan untuk pencarian pakar berdasarkan bidang keilmuan (untuk kebutuhan penugasan reviewer atau kolaborasi) | Ketua LPPM, Admin LPPM | S |
| FR-M3-06 | Sistem dapat mengekspor daftar dosen beserta rasio keterlibatan dalam penelitian/PkM (DPR) untuk kebutuhan LKPS | GKM/Prodi | M |

### Modul M4: Sarana-Prasarana & Anggaran

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-M4-01 | Sistem dapat mencatat daftar sarana-prasarana penelitian/PkM (laboratorium, peralatan, pusat kajian) beserta status ketersediaan | Admin LPPM | S |
| FR-M4-02 | Sistem dapat mencatat alokasi anggaran per skema pendanaan per tahun anggaran | Admin LPPM, Ketua LPPM | M |
| FR-M4-03 | Sistem dapat menampilkan sisa anggaran per skema secara real-time berdasarkan realisasi pencairan pada modul Kontrak | Sistem | S |
| FR-M4-04 | Sistem dapat menghasilkan laporan realisasi anggaran penelitian/PkM per tahun untuk kebutuhan pelaporan institusi | Ketua LPPM, Pimpinan | M |

---

## FASE 2 — PROSES

### Modul P1: Pengajuan Proposal Online

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P1-01 | Sistem menyediakan formulir pengajuan proposal penelitian/PkM secara daring, mencakup judul, skema, bidang, anggota tim, dan RAB | Dosen | M |
| FR-P1-02 | Sistem memungkinkan dosen mengunggah dokumen proposal lengkap (PDF) beserta lampiran pendukung | Dosen | M |
| FR-P1-03 | Sistem menyediakan alur status proposal yang dapat dipantau (draft → diajukan → direview → disetujui/ditolak) | Dosen, Sistem | M |
| FR-P1-04 | Sistem memvalidasi kelengkapan dokumen wajib sebelum proposal dapat diajukan (mis. mitra, RAB, susunan tim) | Sistem | S |
| FR-P1-05 | Sistem memungkinkan dosen mendaftarkan mahasiswa sebagai anggota tim penelitian/PkM langsung pada formulir proposal | Dosen | M |
| FR-P1-06 | Sistem mengirim notifikasi otomatis kepada pengaju saat status proposal berubah | Sistem | S |
| FR-P1-07 | Sistem membatasi jumlah pengajuan proposal per dosen sesuai kebijakan kuota yang ditetapkan LPPM | Sistem | C |

### Modul P2: Review & Penetapan

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P2-01 | Sistem dapat menugaskan satu atau lebih reviewer ke proposal tertentu berdasarkan kesesuaian bidang kepakaran | Admin LPPM, Ketua LPPM | M |
| FR-P2-02 | Sistem menyediakan formulir penilaian terstruktur (skor per kriteria, komentar) bagi reviewer | Reviewer | M |
| FR-P2-03 | Sistem menyimpan seluruh hasil penilaian secara permanen sebagai bukti integritas proses (audit trail, tidak dapat diedit setelah difinalisasi) | Sistem | M |
| FR-P2-04 | Sistem dapat menghasilkan rekapitulasi skor dan peringkat proposal untuk mendukung rapat penetapan pemenang | Ketua LPPM | M |
| FR-P2-05 | Sistem dapat menerbitkan Surat Keputusan (SK) penetapan pemenang hibah secara otomatis berdasarkan hasil rekapitulasi | Admin LPPM | S |
| FR-P2-06 | Sistem mencatat legalitas pengangkatan reviewer (SK reviewer) sebagai bukti kompetensi penilai | Admin LPPM | S |

### Modul P3: Klirens Etik Penelitian

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P3-01 | Sistem menyediakan formulir pengajuan klirens etik untuk penelitian yang melibatkan subjek manusia/hewan/risiko tertentu | Dosen | M |
| FR-P3-02 | Sistem dapat mencatat status permohonan etik (diajukan → ditinjau → disetujui/ditolak) beserta nomor sertifikat etik | Admin LPPM, Komite Etik | M |
| FR-P3-03 | Sistem dapat menyimpan tautan/rujukan ke pengajuan pada Komisi Etik BRIN apabila institusi belum memiliki komite etik internal | Dosen, Admin LPPM | S |
| FR-P3-04 | Sistem memvalidasi bahwa proposal berisiko etik tidak dapat memasuki tahap kontrak sebelum sertifikat etik terunggah | Sistem | S |

### Modul P4: Kontrak & Pencairan Dana

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P4-01 | Sistem dapat menghasilkan draf kontrak/SK penugasan otomatis dari data proposal yang telah disetujui | Admin LPPM | M |
| FR-P4-02 | Sistem dapat mencatat jadwal dan termin pencairan dana (tahap 1, tahap 2, dst.) beserta status pencairan | Admin LPPM | M |
| FR-P4-03 | Sistem dapat mencatat legalitas kerja sama peneliti (perjanjian kerja sama dengan mitra/pihak ketiga) | Admin LPPM | S |
| FR-P4-04 | Sistem mengirim notifikasi pengingat kepada dosen dan admin menjelang tenggat pencairan/pelaporan termin | Sistem | C |

### Modul P5: Monitoring dan Evaluasi (Monev)

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P5-01 | Sistem menyediakan formulir logbook kemajuan yang diisi dosen secara periodik (bulanan/tengah tahun) | Dosen | M |
| FR-P5-02 | Sistem dapat menjadwalkan sesi monev (presentasi/lapangan) dan mencatat berita acara hasil monev | Admin LPPM, Reviewer | M |
| FR-P5-03 | Sistem dapat mencatat catatan tindak lanjut/perbaikan yang diberikan pada saat monev dan status penyelesaiannya | Reviewer, Dosen | S |
| FR-P5-04 | Sistem mengirim notifikasi pengingat pengisian logbook yang mendekati tenggat maupun yang terlambat | Sistem | S |
| FR-P5-05 | Sistem dapat menampilkan dashboard kepatuhan pengisian logbook seluruh peneliti aktif bagi Ketua LPPM | Ketua LPPM | C |

### Modul P6: Keterlibatan Mahasiswa

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P6-01 | Sistem dapat mencatat mahasiswa sebagai anggota tim penelitian/PkM beserta peran (asisten riset, penulis pendamping, dll.) | Dosen | M |
| FR-P6-02 | Sistem dapat menautkan keterlibatan mahasiswa dengan tugas akhir/skripsi atau program MBKM yang sedang ditempuh | Dosen, Mahasiswa | S |
| FR-P6-03 | Sistem dapat menghasilkan rekap keterlibatan mahasiswa per tahun untuk keperluan verifikasi silang dengan data PDDikti | Admin LPPM | M |
| FR-P6-04 | Mahasiswa dapat melihat riwayat keterlibatannya sendiri dalam kegiatan penelitian/PkM melalui akun masing-masing | Mahasiswa | C |

### Modul P7: Integrasi Kurikulum

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-P7-01 | Sistem menyediakan formulir pencatatan pemanfaatan hasil penelitian/PkM ke dalam RPS atau materi mata kuliah tertentu | Dosen | S |
| FR-P7-02 | Sistem dapat menautkan luaran penelitian/PkM (publikasi, produk) sebagai bukti pendukung integrasi kurikulum | Dosen | S |
| FR-P7-03 | Sistem dapat menghasilkan rekap integrasi kurikulum per prodi sebagai bukti sahih untuk LED/LKPS | GKM/Prodi | M |

---

## FASE 3 — LUARAN

### Modul L1: Repositori Publikasi

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-L1-01 | Sistem menyediakan formulir input publikasi (judul, nama jurnal/prosiding, indeksasi, tahun, DOI/tautan) | Dosen | M |
| FR-L1-02 | Sistem memvalidasi format DOI/tautan yang dimasukkan agar dapat diverifikasi kevalidannya | Sistem | S |
| FR-L1-03 | Sistem dapat mengklasifikasikan publikasi berdasarkan tingkat indeksasi (Scopus, SINTA 1-6, nasional non-SINTA) | Sistem | M |
| FR-L1-04 | Sistem menyediakan opsi pemilihan lisensi terbuka (mis. CC-BY) saat unggah luaran yang didanai dana publik | Dosen | S |
| FR-L1-05 | Sistem dapat menautkan publikasi dengan proposal/kegiatan penelitian asal serta anggota tim (termasuk mahasiswa) | Dosen | M |
| FR-L1-06 | Sistem dapat menghitung otomatis indikator PPID (rasio publikasi terhadap jumlah dosen) per prodi/institusi | Sistem | M |
| FR-L1-07 | Sistem menyediakan fitur pencarian dan filter publikasi berdasarkan tahun, prodi, indeksasi, dan penulis | Semua pengguna internal | C |

### Modul L2: HKI & Paten

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-L2-01 | Sistem menyediakan formulir pencatatan pengajuan HKI (jenis: paten, hak cipta, desain industri, dll.) | Dosen | M |
| FR-L2-02 | Sistem dapat mencatat status proses HKI (diajukan → diperiksa substantif → terbit/ditolak) beserta nomor sertifikat | Admin LPPM | M |
| FR-L2-03 | Sistem dapat menautkan HKI dengan penelitian/PkM asal dan tim pengusul | Dosen, Admin LPPM | S |
| FR-L2-04 | Sistem mengirim notifikasi pengingat perpanjangan/pembayaran biaya tahunan HKI yang telah terbit | Sistem | C |

### Modul L3: Produk/Purwarupa & Adopsi

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-L3-01 | Sistem menyediakan formulir pencatatan produk/purwarupa/teknologi tepat guna hasil penelitian atau PkM | Dosen | M |
| FR-L3-02 | Sistem mewajibkan pengisian data mitra/pihak pengguna sebagai bukti adopsi produk oleh masyarakat/industri | Dosen | M |
| FR-L3-03 | Sistem dapat menghitung otomatis indikator PKID (rasio karya yang diadopsi terhadap jumlah dosen) | Sistem | M |
| FR-L3-04 | Sistem dapat menyimpan dokumen bukti pendukung adopsi (surat keterangan mitra, foto, testimoni) | Dosen | S |

### Modul L4: Laporan Akhir & SPJ

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-L4-01 | Sistem menyediakan templat laporan akhir yang terisi otomatis dari data proposal dan realisasi kegiatan | Dosen | M |
| FR-L4-02 | Sistem menyediakan formulir pertanggungjawaban keuangan (SPJ) yang tertaut dengan data pencairan dana pada modul Kontrak | Dosen, Admin Keuangan | M |
| FR-L4-03 | Sistem dapat memverifikasi kelengkapan laporan akhir sebelum status kegiatan ditutup/selesai | Admin LPPM | S |
| FR-L4-04 | Sistem dapat mengarsipkan seluruh laporan akhir dan SPJ secara terpusat dan dapat diunduh untuk keperluan audit | Admin LPPM | M |

---

## FASE 4 — DAMPAK

### Modul D1: Sitasi & Rekognisi

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-D1-01 | Sistem menyediakan fitur impor data sitasi secara berkala dari berkas ekspor Google Scholar/SINTA/Scopus (format CSV) | Admin LPPM | M |
| FR-D1-02 | Sistem dapat menampilkan jumlah sitasi per dosen dan per publikasi, terklasifikasi per tahun | Sistem | M |
| FR-D1-03 | Sistem menyediakan formulir pencatatan rekognisi non-sitasi (penghargaan, undangan sebagai keynote/pakar, jabatan guru besar) | Dosen, Admin LPPM | S |
| FR-D1-04 | Sistem dapat menghasilkan rekap sitasi dan rekognisi 3 tahun terakhir untuk kebutuhan pembuktian tren pada LED | GKM/Prodi | M |

### Modul D2: Kerja Sama & Mitra

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-D2-01 | Sistem menyediakan basis data MoU/MoA penelitian dan PkM beserta nama mitra, ruang lingkup, dan masa berlaku | Admin LPPM | M |
| FR-D2-02 | Sistem dapat menandai status keberlanjutan kerja sama (aktif, berakhir, diperpanjang) | Admin LPPM | S |
| FR-D2-03 | Sistem mengirim notifikasi otomatis menjelang masa berlaku kerja sama berakhir | Sistem | C |
| FR-D2-04 | Sistem dapat menautkan kerja sama dengan kegiatan penelitian/PkM dan luaran yang dihasilkan dari kerja sama tersebut | Admin LPPM | S |

### Modul D3: Adopsi Masyarakat/Industri (Survei Dampak)

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-D3-01 | Sistem dapat mengirimkan formulir survei dampak/kepuasan kepada mitra pasca-kegiatan PkM (via tautan/email) | Sistem, Admin LPPM | S |
| FR-D3-02 | Sistem dapat menyimpan hasil survei dan testimoni mitra sebagai bukti dampak nyata | Mitra Eksternal | S |
| FR-D3-03 | Sistem dapat merekap hasil survei dampak per periode untuk kebutuhan evaluasi dan pelaporan | Ketua LPPM | C |

### Modul D4: Dashboard Analitik Tren

| Kode | Kebutuhan Fungsional | Aktor | Prioritas |
|---|---|---|---|
| FR-D4-01 | Sistem menampilkan dashboard tren indikator kunci (publikasi, HKI, sitasi, dana, keterlibatan mahasiswa) selama 3–5 tahun terakhir | Pimpinan, Ketua LPPM | M |
| FR-D4-02 | Sistem dapat memfilter dashboard berdasarkan prodi, bidang keilmuan, atau skema pendanaan | Semua pengguna internal | S |
| FR-D4-03 | Sistem dapat mengekspor seluruh tabel indikator (PPID, PKID, sitasi, HKI, dsb.) langsung dalam format yang sesuai templat LKPS | GKM/Prodi, Admin LPPM | M |
| FR-D4-04 | Sistem dapat membandingkan capaian tahun berjalan terhadap target pada peta jalan (modul M1) | Ketua LPPM, Pimpinan | C |

---

## Kebutuhan Non-Fungsional Pendukung

| Kategori | Kebutuhan |
|---|---|
| **Keamanan** | Autentikasi berbasis peran (RBAC), enkripsi kata sandi, log audit setiap perubahan data penting (proposal, penilaian, luaran) |
| **Ketersediaan Data** | Backup basis data terjadwal minimal harian; kemampuan pemulihan data (restore) |
| **Kegunaan (Usability)** | Antarmuka sederhana, dapat dioperasikan oleh admin dengan pelatihan minimal (≤ 1 hari) |
| **Interoperabilitas** | Fitur ekspor/impor CSV/Excel/PDF terstandar untuk sinkronisasi manual dengan PDDikti, BIMA, SINTA |
| **Kinerja** | Waktu muat halaman utama dan dashboard tidak lebih dari 3 detik pada beban normal (≤100 pengguna bersamaan) |
| **Skalabilitas** | Basis data dan arsitektur mendukung penambahan modul baru tanpa perombakan struktur inti |
| **Kepatuhan** | Seluruh proses (review, monev, penilaian) menghasilkan jejak audit yang tidak dapat dihapus, sesuai prinsip integritas dalam IAPT 4.1/IAPS 5.1 |

---

## Ringkasan Prioritas Implementasi

Total kebutuhan fungsional tersusun dari 16 modul, dengan distribusi prioritas sebagai berikut — dapat dijadikan acuan penyusunan backlog pengembangan sesuai peta jalan implementasi bertahap yang telah dirancang sebelumnya:

| Fase | Jumlah FR "Must Have" | Fokus Tahap Implementasi |
|---|---|---|
| Masukan | 8 dari 20 FR | Tahap 1 (0–3 bulan) |
| Proses | 15 dari 26 FR | Tahap 2 (3–6 bulan) |
| Luaran | 8 dari 19 FR | Tahap 3 (6–9 bulan) |
| Dampak | 5 dari 15 FR | Tahap 4 (9–12 bulan) |

Kebutuhan berkategori "Must Have" pada tiap fase sebaiknya diselesaikan lebih dahulu pada tahap implementasi terkait, sebelum tim pengembang melanjutkan ke kebutuhan "Should Have" dan "Could Have" yang bersifat penyempurnaan.
