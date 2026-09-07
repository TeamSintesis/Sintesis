# Kamus Data SIMPPM
### Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat

Dokumen ini melengkapi *ER Diagram SIMPPM* dengan rincian setiap entitas: nama atribut, tipe data, keterangan, dan status kunci (Primary Key/Foreign Key). Struktur data disusun mengikuti 4 fase SIMPPM — Masukan, Proses, Luaran, Dampak — dan konsisten dengan modul-modul pada *Spesifikasi Kebutuhan Fungsional (SKF) SIMPPM*.

**Notasi tipe data:** `INT` (bilangan bulat/ID), `VARCHAR(n)` (teks pendek), `TEXT` (teks panjang), `DATE`/`YEAR` (tanggal/tahun), `DECIMAL` (nilai uang/angka desimal), `BOOLEAN`, `ENUM(...)` (nilai tetap terbatas), `URL` (tautan/berkas).

---

## Fase Masukan

### PRODI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_prodi | INT | PK | Identitas unik program studi |
| nama_prodi | VARCHAR(100) | | Nama program studi |
| fakultas | VARCHAR(100) | | Nama fakultas/unit induk |

### DOSEN
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| nidn | VARCHAR(10) | PK | Nomor Induk Dosen Nasional |
| id_prodi | INT | FK → PRODI | Program studi homebase dosen |
| nama | VARCHAR(100) | | Nama lengkap dosen |
| jabatan_fungsional | VARCHAR(50) | | Jabatan fungsional akademik (Asisten Ahli s.d. Guru Besar) |
| bidang_kepakaran | VARCHAR(150) | | Bidang keilmuan/kepakaran utama |
| id_sinta | VARCHAR(20) | | ID SINTA (BRIN) |
| scopus_id | VARCHAR(20) | | Scopus Author ID |
| orcid | VARCHAR(25) | | ORCID identifier |

### MAHASISWA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| nim | VARCHAR(15) | PK | Nomor Induk Mahasiswa |
| id_prodi | INT | FK → PRODI | Program studi mahasiswa |
| nama | VARCHAR(100) | | Nama lengkap mahasiswa |

### RENSTRA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_renstra | INT | PK | Identitas unik dokumen renstra |
| jenis | ENUM(penelitian, PkM) | | Jenis renstra |
| periode_mulai | YEAR | | Tahun awal periode berlaku |
| periode_akhir | YEAR | | Tahun akhir periode berlaku |
| dokumen_url | URL | | Berkas PDF renstra yang diunggah |

### PETA_JALAN
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_peta_jalan | INT | PK | Identitas unik peta jalan |
| id_renstra | INT | FK → RENSTRA | Renstra acuan |
| id_prodi | INT | FK → PRODI | Prodi/bidang keilmuan terkait |
| bidang_keilmuan | VARCHAR(150) | | Bidang keilmuan/tema unggulan |
| tahun | YEAR | | Tahun capaian yang direncanakan |

### PEDOMAN
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_pedoman | INT | PK | Identitas unik dokumen pedoman |
| jenis | VARCHAR(50) | | Pedoman penelitian/PkM/kode etik |
| versi | VARCHAR(20) | | Nomor versi dokumen |
| tanggal_berlaku | DATE | | Tanggal pedoman berlaku |
| dokumen_url | URL | | Berkas pedoman |

### SARANA_PRASARANA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_sarpras | INT | PK | Identitas unik sarana/prasarana |
| nama_sarpras | VARCHAR(150) | | Nama laboratorium/peralatan/pusat kajian |
| lokasi | VARCHAR(100) | | Lokasi/unit penempatan |
| status | ENUM(tersedia, digunakan, rusak) | | Status ketersediaan |

### SKEMA_PENDANAAN
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_skema | INT | PK | Identitas unik skema hibah |
| nama_skema | VARCHAR(100) | | Nama skema pendanaan |
| jenis | ENUM(internal, eksternal) | | Sumber pendanaan |
| plafon_dana | DECIMAL(15,2) | | Batas maksimum dana per proposal |
| sumber_dana | VARCHAR(100) | | Instansi pemberi dana |

### MITRA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_mitra | INT | PK | Identitas unik mitra |
| nama_mitra | VARCHAR(150) | | Nama institusi/individu mitra |
| jenis_mitra | ENUM(industri, pemda, masyarakat, PT lain) | | Kategori mitra |
| kontak | VARCHAR(100) | | Kontak person/nomor telepon/email |
| alamat | VARCHAR(200) | | Alamat mitra |

---

## Fase Proses

### PROPOSAL
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_proposal | INT | PK | Identitas unik proposal |
| nidn_pengusul | VARCHAR(10) | FK → DOSEN | Dosen pengusul/ketua tim |
| id_skema | INT | FK → SKEMA_PENDANAAN | Skema hibah yang diajukan |
| id_peta_jalan | INT | FK → PETA_JALAN | Kesesuaian dengan peta jalan (nullable) |
| judul | VARCHAR(250) | | Judul penelitian/PkM |
| jenis | ENUM(penelitian, PkM) | | Jenis kegiatan |
| tahun_usulan | YEAR | | Tahun pengajuan |
| status | ENUM(draft, diajukan, direview, disetujui, ditolak) | | Status alur proposal |
| rab_total | DECIMAL(15,2) | | Total Rencana Anggaran Biaya |

### ANGGOTA_TIM
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_anggota | INT | PK | Identitas unik baris anggota tim |
| id_proposal | INT | FK → PROPOSAL | Proposal terkait |
| nidn | VARCHAR(10) | FK → DOSEN | Diisi bila anggota adalah dosen (nullable) |
| nim | VARCHAR(15) | FK → MAHASISWA | Diisi bila anggota adalah mahasiswa (nullable) |
| peran | VARCHAR(50) | | Peran dalam tim (anggota, asisten riset, dst.) |

### PENILAIAN
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_penilaian | INT | PK | Identitas unik hasil penilaian |
| id_proposal | INT | FK → PROPOSAL | Proposal yang dinilai |
| nidn_reviewer | VARCHAR(10) | FK → DOSEN | Reviewer penilai |
| skor | DECIMAL(5,2) | | Skor total hasil penilaian |
| komentar | TEXT | | Catatan/masukan reviewer |
| status_finalisasi | BOOLEAN | | Menandai apakah skor sudah dikunci (audit trail) |
| tanggal | DATE | | Tanggal penilaian |

### KLIRENS_ETIK
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_etik | INT | PK | Identitas unik pengajuan etik |
| id_proposal | INT | FK → PROPOSAL | Proposal berisiko etik terkait |
| status | ENUM(diajukan, ditinjau, disetujui, ditolak) | | Status permohonan |
| nomor_sertifikat | VARCHAR(50) | | Nomor sertifikat etik (bila terbit) |
| tanggal_terbit | DATE | | Tanggal sertifikat etik terbit |

### KONTRAK
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_kontrak | INT | PK | Identitas unik kontrak |
| id_proposal | INT | FK → PROPOSAL | Proposal yang dikontrak |
| nomor_sk | VARCHAR(50) | | Nomor SK penugasan/kontrak |
| tanggal_kontrak | DATE | | Tanggal kontrak diterbitkan |
| nilai_kontrak | DECIMAL(15,2) | | Nilai total kontrak |

### PENCAIRAN_DANA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_pencairan | INT | PK | Identitas unik transaksi pencairan |
| id_kontrak | INT | FK → KONTRAK | Kontrak terkait |
| termin | INT | | Nomor termin pencairan (1, 2, dst.) |
| jumlah | DECIMAL(15,2) | | Jumlah dana dicairkan |
| tanggal | DATE | | Tanggal pencairan |
| status | ENUM(dijadwalkan, dicairkan, tertunda) | | Status pencairan |

### LOGBOOK
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_logbook | INT | PK | Identitas unik entri logbook |
| id_proposal | INT | FK → PROPOSAL | Kegiatan terkait |
| periode | VARCHAR(20) | | Periode pelaporan (bulan/tengah tahun) |
| isi_kemajuan | TEXT | | Uraian kemajuan kegiatan |
| tanggal_isi | DATE | | Tanggal pengisian |

### MONEV
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_monev | INT | PK | Identitas unik sesi monev |
| id_proposal | INT | FK → PROPOSAL | Kegiatan yang dimonitor |
| nidn_reviewer | VARCHAR(10) | FK → DOSEN | Reviewer/pemonev |
| catatan | TEXT | | Catatan tindak lanjut/perbaikan |
| status | ENUM(terjadwal, selesai) | | Status sesi monev |

### INTEGRASI_KURIKULUM
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_integrasi | INT | PK | Identitas unik entri integrasi |
| id_proposal | INT | FK → PROPOSAL | Sumber hasil penelitian/PkM |
| mata_kuliah | VARCHAR(100) | | Mata kuliah yang memanfaatkan hasil |
| rps_tautan | URL | | Tautan/berkas RPS pendukung |

---

## Fase Luaran

### PUBLIKASI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_publikasi | INT | PK | Identitas unik publikasi |
| id_proposal | INT | FK → PROPOSAL | Proposal/kegiatan asal |
| judul | VARCHAR(250) | | Judul artikel/karya |
| jurnal_prosiding | VARCHAR(150) | | Nama jurnal/prosiding |
| indeksasi | ENUM(Scopus, SINTA 1-6, Nasional non-SINTA) | | Tingkat indeksasi |
| tahun | YEAR | | Tahun publikasi |
| doi | VARCHAR(100) | | DOI/tautan publikasi |
| lisensi | VARCHAR(20) | | Lisensi terbuka (misal CC-BY) |

### HKI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_hki | INT | PK | Identitas unik pengajuan HKI |
| id_proposal | INT | FK → PROPOSAL | Proposal/kegiatan asal |
| jenis | ENUM(paten, hak_cipta, desain_industri, dll.) | | Jenis kekayaan intelektual |
| status | ENUM(diajukan, diperiksa_substantif, terbit, ditolak) | | Status proses HKI |
| nomor_sertifikat | VARCHAR(50) | | Nomor sertifikat HKI (bila terbit) |

### PRODUK_ADOPSI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_produk | INT | PK | Identitas unik produk/purwarupa |
| id_proposal | INT | FK → PROPOSAL | Proposal/kegiatan asal |
| id_mitra | INT | FK → MITRA | Mitra/pihak pengguna produk |
| nama_produk | VARCHAR(150) | | Nama produk/purwarupa/TTG |
| bukti_adopsi | URL | | Dokumen bukti adopsi (surat, foto, testimoni) |

### LAPORAN_AKHIR
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_laporan | INT | PK | Identitas unik laporan akhir |
| id_proposal | INT | FK → PROPOSAL | Proposal/kegiatan terkait |
| dokumen_url | URL | | Berkas laporan akhir |
| tanggal_submit | DATE | | Tanggal pengunggahan laporan |
| status_verifikasi | ENUM(belum, terverifikasi) | | Status verifikasi kelengkapan |

### SPJ
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_spj | INT | PK | Identitas unik SPJ |
| id_laporan | INT | FK → LAPORAN_AKHIR | Laporan akhir terkait |
| jumlah_realisasi | DECIMAL(15,2) | | Jumlah realisasi anggaran |
| dokumen_url | URL | | Berkas pertanggungjawaban keuangan |

---

## Fase Dampak

### SITASI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_sitasi | INT | PK | Identitas unik data sitasi |
| id_publikasi | INT | FK → PUBLIKASI | Publikasi yang disitasi (nullable) |
| nidn | VARCHAR(10) | FK → DOSEN | Dosen penulis |
| jumlah_sitasi | INT | | Jumlah sitasi tercatat |
| tahun | YEAR | | Tahun perhitungan sitasi |
| sumber | ENUM(Google Scholar, SINTA, Scopus) | | Sumber data sitasi |

### REKOGNISI
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_rekognisi | INT | PK | Identitas unik data rekognisi |
| nidn | VARCHAR(10) | FK → DOSEN | Dosen penerima rekognisi |
| jenis | VARCHAR(100) | | Jenis rekognisi (penghargaan, keynote, guru besar, dll.) |
| deskripsi | TEXT | | Uraian rekognisi |
| tahun | YEAR | | Tahun perolehan |

### KERJASAMA
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_kerjasama | INT | PK | Identitas unik data kerja sama |
| id_mitra | INT | FK → MITRA | Mitra kerja sama |
| ruang_lingkup | VARCHAR(200) | | Ruang lingkup MoU/MoA |
| tanggal_mulai | DATE | | Tanggal mulai berlaku |
| tanggal_akhir | DATE | | Tanggal akhir berlaku |
| status | ENUM(aktif, berakhir, diperpanjang) | | Status keberlanjutan kerja sama |

### SURVEI_DAMPAK
| Atribut | Tipe | Key | Keterangan |
|---|---|---|---|
| id_survei | INT | PK | Identitas unik entri survei |
| id_mitra | INT | FK → MITRA | Mitra/responden survei |
| id_proposal | INT | FK → PROPOSAL | Kegiatan PkM terkait (nullable) |
| hasil | TEXT | | Ringkasan hasil survei dampak |
| testimoni | TEXT | | Testimoni mitra sebagai bukti dampak nyata |

---

## Ringkasan Struktur

Total **27 entitas** tersusun mengikuti 4 fase SIMPPM, dengan **PROPOSAL** sebagai entitas penghubung utama (hub) yang menautkan seluruh siklus kegiatan penelitian/PkM — dari pengajuan (Fase Proses), pencatatan luaran (Fase Luaran), hingga pengukuran dampak (Fase Dampak). Entitas referensi lintas fase (DOSEN, MAHASISWA, PRODI, MITRA, PROPOSAL, PUBLIKASI, SKEMA_PENDANAAN) digambar ulang dalam kotak putus-putus pada diagram fase lanjutan untuk menjaga keterbacaan tanpa mengulang seluruh atributnya.

| Fase | Jumlah Entitas Inti | Entitas |
|---|---|---|
| Masukan | 9 | PRODI, DOSEN, MAHASISWA, RENSTRA, PETA_JALAN, PEDOMAN, SARANA_PRASARANA, SKEMA_PENDANAAN, MITRA |
| Proses | 9 | PROPOSAL, ANGGOTA_TIM, PENILAIAN, KLIRENS_ETIK, KONTRAK, PENCAIRAN_DANA, LOGBOOK, MONEV, INTEGRASI_KURIKULUM |
| Luaran | 5 | PUBLIKASI, HKI, PRODUK_ADOPSI, LAPORAN_AKHIR, SPJ |
| Dampak | 4 | SITASI, REKOGNISI, KERJASAMA, SURVEI_DAMPAK |
