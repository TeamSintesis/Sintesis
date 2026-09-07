# Model Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat (SIMPPM)
### Untuk Perguruan Tinggi Kecil hingga Menengah

---

## 1. Latar Belakang dan Dasar Regulasi

Pengelolaan penelitian dan pengabdian kepada masyarakat (PPM) di perguruan tinggi kini dituntut untuk terdokumentasi secara sistematis, bukan hanya administratif tahunan. Kebutuhan ini muncul dari beberapa lapis regulasi yang berlaku:

- **UU No. 12 Tahun 2012** tentang Pendidikan Tinggi mewajibkan penelitian dan PkM sebagai bagian tridarma yang harus dikelola secara terencana ([peraturan.bpk.go.id](https://peraturan.bpk.go.id/Details/39063/uu-no-12-tahun-2012)).
- **Permendiktisaintek No. 39 Tahun 2025** tentang Penjaminan Mutu Pendidikan Tinggi menetapkan standar penelitian dan PkM berbasis tiga komponen: masukan, proses, dan luaran ([peraturan.bpk.go.id](https://peraturan.bpk.go.id/Details/333967/permendikti-saintek-no-39-tahun-2025)).
- **PerBAN-PT No. 35 Tahun 2025 (IAPT 4.1)** dan **PerBAN-PT No. 36 Tahun 2025 (IAPS 5.1)** — instrumen akreditasi perguruan tinggi dan program studi terbaru — menilai kriteria *Relevansi Penelitian* dan *Relevansi Pengabdian kepada Masyarakat* melalui empat dimensi: **Masukan → Proses → Luaran/Capaian → Dampak** ([banpt.or.id — IAPS 5.1](https://www.banpt.or.id/wp-content/uploads/2025/12/PerBAN-PT-36-2025-IAPS-5.1-BAN-PT.pdf), [bpm.unair.ac.id — IAPT 4.1](https://bpm.unair.ac.id/ban-pt-terbitkan-peraturan-nomor-35-tahun-2025-tentang-instrumen-akreditasi-perguruan-tinggi/)).
- **Peraturan BRIN No. 22 Tahun 2022** mewajibkan klirens etik untuk penelitian yang melibatkan manusia, hewan, atau risiko tertentu ([peraturan.go.id](https://peraturan.go.id/id/peraturan-brin-no-22-tahun-2022)).
- Standar masukan penelitian secara eksplisit menyebut kebutuhan **"sistem TIK yang andal"** untuk mendokumentasikan, mengevaluasi, melaporkan, dan menyebarluaskan proses dan hasil penelitian — sehingga keberadaan sistem informasi PPM bukan lagi pelengkap, melainkan **syarat mutu** yang dinilai langsung dalam akreditasi.

Dokumen ini merancang **model sistem informasi (SIMPPM)** yang memenuhi kebutuhan minimum sesuai regulasi tersebut, sekaligus realistis diterapkan oleh perguruan tinggi kecil-menengah dengan sumber daya IT dan anggaran terbatas.

---

## 2. Prinsip Desain untuk PT Kecil-Menengah

Mengingat sasarannya adalah PT skala kecil-menengah (bukan PTN besar dengan tim IT khusus), model dirancang dengan lima prinsip:

| Prinsip Desain | Uraian |
|---|---|
| **Modular dan Bertahap** | Sistem dikembangkan secara modular per komponen fungsional, dimulai dari pemenuhan kebutuhan minimum yang bersifat wajib, kemudian dilanjutkan dengan pengembangan fitur tambahan secara bertahap sehingga tidak diperlukan implementasi menyeluruh yang dilakukan sekaligus. |
| **Kesatuan Sumber Data** | Data peneliti, proposal, dan luaran penelitian cukup dimasukkan satu kali, kemudian dapat digunakan kembali untuk keperluan pelaporan Laporan Evaluasi Diri, Laporan Kinerja Program Studi, Beban Kerja Dosen, serta laporan hibah, sehingga meminimalkan pengulangan pemasukan data oleh dosen. |
| **Efisiensi Biaya dan Keterjangkauan Teknologi** | Sistem menggunakan perangkat lunak sumber terbuka dengan arsitektur tunggal yang sederhana, sehingga dapat dioperasikan pada server milik perguruan tinggi maupun server sewa dengan biaya terjangkau. |
| **Interoperabilitas melalui Mekanisme Ekspor dan Impor Data** | Mengingat keterbukaan antarmuka pemrograman aplikasi pada sistem nasional seperti PDDikti, BIMA, dan SINTA masih terbatas, sinkronisasi data dilakukan melalui proses ekspor dan impor berkas dengan format terstandardisasi, yaitu Excel, CSV, atau PDF, yang dapat diunggah secara manual maupun secara terjadwal. |
| **Kesesuaian dengan Kapasitas Sumber Daya Manusia** | Antarmuka sistem dirancang secara sederhana sehingga tidak memerlukan pelatihan yang lama, dan dapat dioperasikan oleh satu hingga dua orang staf Lembaga Penelitian dan Pengabdian kepada Masyarakat atau tenaga administrasi tanpa memerlukan unit teknologi informasi yang besar. |

---

## 3. Arsitektur Umum: Siklus 4 Fase

Mengikuti kerangka penilaian BAN-PT, SIMPPM disusun sebagai siklus tertutup (bukan alur linear searah) — luaran dan dampak yang tercatat menjadi umpan balik untuk memperbarui peta jalan dan rencana strategis pada fase Masukan berikutnya. Seluruh fase terhubung ke satu **basis data terpadu** yang menjadi sumber ekspor ke PDDikti, BIMA, SINTA, Klirens Etik BRIN, dan borang LED/LKPS.

*(Lihat diagram arsitektur pada lampiran gambar.)*

| Fase | Fokus Regulasi | Peran dalam Siklus |
|---|---|---|
| **Masukan** | Renstra, peta jalan, pedoman, standar sarpras & kompetensi | Fondasi kelembagaan sebelum penelitian/PkM dijalankan |
| **Proses** | Integritas review, monev, keterlibatan mahasiswa, integrasi kurikulum | Tata kelola pelaksanaan kegiatan |
| **Luaran** | Publikasi, HKI, produk, lisensi terbuka | Bukti capaian yang terukur |
| **Dampak** | Sitasi, rekognisi, kerja sama, adopsi masyarakat/industri | Bukti relevansi dan pengaruh nyata |

---

## 4. Fase 1 — Masukan (Input)

**Tujuan:** Menjamin dasar kelembagaan tersedia dan terdokumentasi secara digital, sesuai standar masukan penelitian/PkM dalam IAPT 4.1 & IAPS 5.1.

### Modul yang dibutuhkan

| Modul | Fungsi Inti | Kebutuhan Minimum |
|---|---|---|
| **Renstra & Peta Jalan** | Menyimpan dokumen Rencana Strategis Penelitian/PkM dan peta jalan per bidang/prodi, dengan versi & periode berlaku | Upload dokumen PDF + metadata terstruktur (tema unggulan, tahun, sasaran) |
| **Pedoman & Etik** | Repositori pedoman penelitian/PkM, kode etik, SOP HKI — versi terkini otomatis menggantikan versi lama | Sistem versioning dokumen sederhana |
| **Basis Data Peneliti/Pakar** | Profil dosen: bidang kepakaran, jabatan fungsional, ID SINTA/Scopus/ORCID, rekam jejak penelitian & PkM | Terhubung ke data kepegawaian (SDM) agar tidak entri ganda |
| **Sarana-Prasarana & Anggaran** | Inventaris lab/fasilitas riset dan alokasi anggaran per skema pendanaan | Cukup tabel referensi dasar, tidak perlu sistem aset penuh |

### Entitas data kunci
`Renstra`, `PetaJalan(bidang, prodi, tahun)`, `Pedoman(versi, tanggal_berlaku)`, `Peneliti(NIDN, bidang_kepakaran, id_sinta, id_scopus)`, `Sarpras`, `SkemaPendanaan`

---

## 5. Fase 2 — Proses

**Tujuan:** Memastikan proses penelitian/PkM berintegritas, transparan, terdokumentasi, dan melibatkan mahasiswa serta kurikulum sesuai tuntutan akreditasi.

### Modul yang Dibutuhkan

| Modul | Fungsi Inti | Kebutuhan Minimum |
|---|---|---|
| **Pengajuan Proposal Online** | Dosen mengajukan proposal, memilih skema pendanaan, melampirkan Rencana Anggaran Biaya, dan mendaftarkan mahasiswa yang terlibat dalam kegiatan | Formulir elektronik dengan status pengajuan yang meliputi tahap penyusunan, pengajuan, dan peninjauan secara berurutan |
| **Review dan Penetapan** | Penugasan reviewer, formulir penilaian terstruktur, pendokumentasian skor, serta penerbitan Surat Keputusan penetapan penerima hibah | Sistem wajib menyediakan pencatatan identitas penilai beserta objek yang dinilai sebagai bukti integritas proses |
| **Klirens Etik** | Pengajuan permohonan kelayakan etik penelitian untuk kegiatan yang berisiko terhadap subjek manusia atau hewan | Formulir pengajuan disertai status persetujuan, dengan kemungkinan rujukan kepada Komisi Etik Badan Riset dan Inovasi Nasional apabila perguruan tinggi belum memiliki komite etik tersendiri |
| **Kontrak dan Pencairan Dana** | Penerbitan Surat Keputusan penugasan atau kontrak, beserta penjadwalan pencairan dana secara bertahap | Templat kontrak yang dihasilkan secara otomatis berdasarkan data proposal yang telah disetujui |
| **Pemantauan dan Evaluasi** | Pencatatan logbook kemajuan, laporan perkembangan, penjadwalan kegiatan pemantauan dan evaluasi, serta catatan tindak lanjut | Formulir logbook yang diisi secara berkala, baik bulanan maupun tengah tahunan, dengan notifikasi pengingat |
| **Keterlibatan Mahasiswa** | Pencatatan mahasiswa sebagai anggota tim penelitian atau pengabdian kepada masyarakat, serta penautan dengan tugas akhir, skripsi, atau program Merdeka Belajar Kampus Merdeka | Kolom status keterlibatan yang wajib diisi agar dapat diverifikasi silang dengan data Pangkalan Data Pendidikan Tinggi |
| **Integrasi Kurikulum** | Pencatatan pemanfaatan hasil penelitian atau pengabdian kepada masyarakat ke dalam Rencana Pembelajaran Semester atau materi perkuliahan | Formulir sederhana yang mencakup mata kuliah, dosen pengampu, dan luaran penelitian yang diintegrasikan |

### Entitas Data Kunci
Proposal dengan atribut status, skema, dan Rencana Anggaran Biaya; Reviewer; Penilaian Proposal; Klirens Etik; Kontrak; Logbook; Keterlibatan Mahasiswa dengan atribut Nomor Induk Mahasiswa dan peran; serta Integrasi Kurikulum dengan atribut mata kuliah dan luaran terkait.

---

## 6. Fase 3. Luaran

**Tujuan:** Mendokumentasikan seluruh capaian yang dapat diukur, meliputi publikasi, kekayaan intelektual, dan produk, sebagai bukti utama dalam Laporan Kinerja Program Studi.

### Modul yang Dibutuhkan

| Modul | Fungsi Inti | Kebutuhan Minimum |
|---|---|---|
| **Repositori Publikasi** | Katalog artikel jurnal atau prosiding beserta metadata, meliputi nama jurnal, indeksasi SINTA atau Scopus, Digital Object Identifier, dan tautan akses terbuka | Formulir input disertai validasi otomatis terhadap format Digital Object Identifier atau tautan |
| **Kekayaan Intelektual dan Paten** | Pencatatan status pengajuan hingga penerbitan sertifikat kekayaan intelektual, termasuk paten dan hak cipta | Kolom status yang mencakup tahap pengajuan, pemeriksaan, dan penerbitan secara berurutan |
| **Produk, Purwarupa, dan Adopsi** | Pendokumentasian produk teknologi tepat guna, modul, atau sistem hasil pengabdian kepada masyarakat beserta pihak yang mengadopsinya | Kolom mitra pengguna yang wajib diisi sebagai bukti adopsi |
| **Laporan Akhir dan Surat Pertanggungjawaban** | Pengunggahan laporan akhir dan surat pertanggungjawaban keuangan yang terstandardisasi | Templat laporan yang dihasilkan secara otomatis berdasarkan data proposal dan realisasi anggaran |

### Entitas Data Kunci
Publikasi dengan atribut indeksasi, Digital Object Identifier, dan penulis; Kekayaan Intelektual dengan atribut jenis, status, dan nomor sertifikat; Produk dengan atribut jenis dan mitra pengguna; Laporan Akhir; serta Realisasi Anggaran.

> Catatan kepatuhan. Luaran yang dibiayai dari sumber pendanaan publik idealnya diberikan lisensi terbuka sesuai anjuran Instrumen Akreditasi Perguruan Tinggi 4.1 dan Instrumen Akreditasi Program Studi 5.1. Modul repositori sebaiknya menyediakan pilihan lisensi, misalnya lisensi Creative Commons Attribution, pada saat pengunggahan.

---

## 7. Fase 4. Dampak

**Tujuan:** Membuktikan relevansi nyata penelitian dan pengabdian kepada masyarakat, yang merupakan bagian paling sering terlewat oleh perguruan tinggi skala kecil hingga menengah karena sifatnya yang berjangka panjang dan melibatkan berbagai sistem eksternal.

### Modul yang Dibutuhkan

| Modul | Fungsi Inti | Kebutuhan Minimum |
|---|---|---|
| **Sitasi dan Rekognisi** | Pencatatan jumlah sitasi yang diimpor secara manual maupun berkala dari Google Scholar, SINTA, atau Scopus, beserta penghargaan dan undangan sebagai pembicara utama atau pakar | Impor berkas terstandar secara berkala, mengingat keterbukaan antarmuka pemrograman aplikasi untuk data sitasi publik masih terbatas |
| **Kerja Sama dan Mitra** | Basis data nota kesepahaman dan perjanjian kerja sama penelitian dan pengabdian kepada masyarakat, beserta status keberlanjutannya | Kolom durasi dan status keaktifan kerja sama untuk memantau keberlanjutan |
| **Adopsi Masyarakat dan Industri** | Survei atau testimoni dampak dari mitra maupun masyarakat penerima manfaat kegiatan pengabdian | Formulir survei sederhana yang dikirimkan kepada mitra setelah kegiatan selesai dilaksanakan |
| **Dasbor Analitik Tren** | Visualisasi tren capaian selama tiga hingga lima tahun untuk indikator kunci akreditasi | Grafik yang dihasilkan secara otomatis dari data pada Fase Luaran, tanpa memerlukan perangkat analitik bisnis berbiaya tinggi karena cukup menggunakan dasbor bawaan sistem |

### Entitas Data Kunci
Sitasi dengan atribut sumber, jumlah, dan tahun; Rekognisi; Kerja Sama dengan atribut mitra, status, dan durasi; Survei Dampak; serta Tren Indikator.

---

## 8. Pemetaan terhadap Indikator Kinerja Akreditasi

Dasbor sistem sebaiknya secara otomatis menghitung indikator berikut, karena dirujuk langsung dalam Laporan Kinerja Program Studi pada Instrumen Akreditasi Perguruan Tinggi 4.1 dan Instrumen Akreditasi Program Studi 5.1.

| Indikator | Sumber Data dalam SIMPPM | Fase Asal |
|---|---|---|
| **PPID** (persentase publikasi terhadap jumlah DPR) | Modul Repositori Publikasi dibagi dengan Basis Data Peneliti | Luaran |
| **PKID** (persentase karya yang diadopsi oleh masyarakat atau industri) | Modul Produk, Purwarupa, dan Adopsi | Luaran dan Dampak |
| **Sitasi** | Modul Sitasi dan Rekognisi | Dampak |
| **HKI** | Modul Kekayaan Intelektual dan Paten | Luaran |
| **Realisasi dana penelitian dan pengabdian** | Modul Kontrak dan Pencairan Dana, serta Laporan Akhir | Proses dan Luaran |
| **Keterlibatan mahasiswa** | Modul Keterlibatan Mahasiswa, diverifikasi silang dengan Pangkalan Data Pendidikan Tinggi | Proses |
| **Rekognisi kepakaran** | Modul Sitasi dan Rekognisi | Dampak |
| **Keberlanjutan kerja sama** | Modul Kerja Sama dan Mitra | Dampak |

Kemampuan menghasilkan tabel tersebut secara otomatis untuk keperluan Laporan Evaluasi Diri dan Laporan Kinerja Program Studi merupakan manfaat paling signifikan dari sistem bagi perguruan tinggi skala kecil hingga menengah, karena tahapan tersebut umumnya merupakan bagian yang paling memakan waktu dalam persiapan akreditasi.

---

## 9. Peran Pengguna Berdasarkan Kendali Akses

| Peran | Hak Akses Utama |
|---|---|
| **Admin LPPM** | Mengelola seluruh modul, mengelola pengguna, dan mengekspor data |
| **Ketua atau Kepala LPPM** | Memberikan persetujuan proposal, menetapkan penerima hibah, dan mengakses laporan agregat |
| **Reviewer** | Mengakses proposal yang ditugaskan beserta formulir penilaian |
| **Dosen atau Peneliti** | Mengajukan proposal, mengisi logbook, mengunggah luaran, dan melihat profil sendiri |
| **Mahasiswa** | Mendaftarkan keterlibatan dalam tim serta melihat status kegiatan yang diikuti |
| **Program Studi atau Gugus Kendali Mutu** | Memiliki akses baca untuk mengekspor data pendukung Laporan Evaluasi Diri dan Laporan Kinerja Program Studi per program studi |
| **Pimpinan, yaitu Rektor atau Wakil Rektor Bidang Riset** | Mengakses dasbor ringkasan dan tren tanpa kewenangan memasukkan data |
| **Mitra Eksternal, bersifat opsional** | Memiliki akses terbatas untuk mengisi survei dampak kerja sama |

---

## 10. Rekomendasi Arsitektur Teknologi

Arsitektur dirancang agar terjangkau bagi perguruan tinggi skala kecil hingga menengah yang tidak memiliki unit teknologi informasi berskala besar.

- **Pola arsitektur.** Aplikasi web dengan struktur tunggal yang modular, bukan arsitektur layanan terdistribusi, sehingga lebih mudah dikelola oleh satu hingga dua tenaga administrator.
- **Kerangka kerja yang disarankan.** Kerangka kerja pengembangan perangkat lunak sumber terbuka seperti Laravel berbasis bahasa PHP atau Django berbasis bahasa Python, dengan sistem basis data PostgreSQL atau MySQL, mengingat ketersediaannya bebas biaya lisensi, dokumentasinya lengkap, dan sumber daya manusia yang menguasainya mudah ditemukan secara lokal.
- **Penempatan sistem.** Server yang telah dimiliki oleh perguruan tinggi, atau server sewa dengan biaya rendah, tanpa memerlukan infrastruktur komputasi awan yang kompleks pada tahap awal implementasi.
- **Sinkronisasi eksternal.** Mengingat keterbukaan antarmuka pemrograman aplikasi publik pada Pangkalan Data Pendidikan Tinggi, BIMA, dan SINTA masih terbatas, sistem perlu menyediakan fitur ekspor dan impor berkas terstandardisasi sebagai penghubung manual maupun terjadwal terhadap sistem tersebut.
- **Tahap pengembangan awal tanpa membangun sistem baru secara menyeluruh.** Perguruan tinggi dengan keterbatasan anggaran yang sangat ketat dapat memulai dengan versi paling minimum menggunakan aplikasi perkantoran daring, misalnya kombinasi formulir elektronik, lembar kerja elektronik, dan penyimpanan berkas daring, sebagai basis data sementara sebelum bermigrasi ke aplikasi web yang lebih lengkap, sehingga transisi dapat dilakukan secara bertahap sesuai kapasitas anggaran yang tersedia.
- **Keamanan dasar.** Kendali akses berbasis peran, pencatatan jejak audit atas setiap perubahan data yang meliputi identitas pengguna, waktu, dan jenis perubahan, serta pencadangan data secara terjadwal.

---

## 11. Peta Jalan Implementasi Bertahap

Peta jalan dirancang agar perguruan tinggi skala kecil hingga menengah dapat memulai dari kebutuhan yang paling mendesak tanpa harus menunggu keseluruhan sistem selesai dibangun.

| Tahap | Durasi Indikatif | Modul yang Dibangun | Nilai Prioritas |
|---|---|---|---|
| **Tahap Pertama, Fondasi** | Nol hingga tiga bulan | Basis Data Peneliti, Rencana Strategis dan Peta Jalan, Pedoman dan Etik | Wajib tersedia untuk memenuhi standar masukan akreditasi |
| **Tahap Kedua, Operasional Inti** | Tiga hingga enam bulan | Pengajuan Proposal, Review dan Penetapan, Kontrak, Pemantauan dan Evaluasi | Menjamin integritas proses yang dinilai oleh Badan Akreditasi Nasional Perguruan Tinggi |
| **Tahap Ketiga, Bukti Luaran** | Enam hingga sembilan bulan | Repositori Publikasi, Kekayaan Intelektual, Produk dan Adopsi, Laporan Akhir | Merupakan data utama untuk pengisian Laporan Kinerja Program Studi |
| **Tahap Keempat, Dampak dan Analitik** | Sembilan hingga dua belas bulan | Sitasi dan Rekognisi, Kerja Sama, Dasbor Tren | Melengkapi bukti dampak untuk status Terakreditasi Unggul |
| **Tahap Kelima, Otomasi Lanjutan** | Lebih dari dua belas bulan | Sinkronisasi terjadwal terhadap Pangkalan Data Pendidikan Tinggi dan SINTA, ekspor otomatis Laporan Evaluasi Diri dan Laporan Kinerja Program Studi | Bersifat opsional untuk efisiensi jangka panjang sesuai kapasitas yang tersedia |

---

## 12. Kesimpulan

Model SIMPPM yang diuraikan di atas dirancang agar perguruan tinggi skala kecil hingga menengah dapat memenuhi tiga kebutuhan sekaligus. Pertama, kepatuhan terhadap standar masukan, proses, luaran, dan dampak sebagaimana diatur dalam Instrumen Akreditasi Perguruan Tinggi 4.1 dan Instrumen Akreditasi Program Studi 5.1. Kedua, efisiensi operasional harian Lembaga Penelitian dan Pengabdian kepada Masyarakat tanpa duplikasi pemasukan data. Ketiga, kesiapan bukti akreditasi dalam bentuk Laporan Evaluasi Diri dan Laporan Kinerja Program Studi yang dapat diekspor sewaktu waktu diperlukan, tanpa harus membangun sistem yang kompleks atau berbiaya tinggi sejak awal. Pendekatan bertahap memungkinkan perguruan tinggi memulai dari modul yang paling mendesak dan berkembang sesuai kapasitas sumber daya yang tersedia.

---

## Referensi

1. UU No. 12 Tahun 2012 tentang Pendidikan Tinggi — [peraturan.bpk.go.id](https://peraturan.bpk.go.id/Details/39063/uu-no-12-tahun-2012)
2. Permendiktisaintek No. 39 Tahun 2025 tentang Penjaminan Mutu Pendidikan Tinggi — [peraturan.bpk.go.id](https://peraturan.bpk.go.id/Details/333967/permendikti-saintek-no-39-tahun-2025)
3. PerBAN-PT No. 36 Tahun 2025 — Instrumen Akreditasi Program Studi (IAPS 5.1) — [banpt.or.id](https://www.banpt.or.id/wp-content/uploads/2025/12/PerBAN-PT-36-2025-IAPS-5.1-BAN-PT.pdf)
4. PerBAN-PT No. 35 Tahun 2025 — Instrumen Akreditasi Perguruan Tinggi (IAPT 4.1) — [bpm.unair.ac.id](https://bpm.unair.ac.id/ban-pt-terbitkan-peraturan-nomor-35-tahun-2025-tentang-instrumen-akreditasi-perguruan-tinggi/)
5. Peraturan BRIN No. 22 Tahun 2022 tentang Klirens Etik Riset — [peraturan.go.id](https://peraturan.go.id/id/peraturan-brin-no-22-tahun-2022)
6. BIMA Kemdiktisaintek (platform pengajuan hibah penelitian & PkM nasional) — [bima.kemdiktisaintek.go.id](https://bima.kemdiktisaintek.go.id)
