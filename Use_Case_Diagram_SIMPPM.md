# Use Case Diagram SIMPPM
### Seluruh Modul — Disusun per Fase (Masukan, Proses, Luaran, Dampak)

Diagram use case berikut melengkapi *Model Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat (SIMPPM)* dan *Spesifikasi Kebutuhan Fungsional (SKF)* yang telah disusun sebelumnya. Untuk menjaga keterbacaan, diagram dipecah menjadi empat bagian sesuai fase siklus SIMPPM — bukan satu diagram tunggal yang memuat seluruh 21 use case dan 8 aktor sekaligus.

**Cara membaca diagram:**
- **Kotak** = batas sistem (system boundary) SIMPPM pada fase tersebut
- **Oval** = use case (fungsi yang dapat dijalankan aktor)
- **Gambar orang** = aktor (pengguna/sistem eksternal)
- **Garis penuh** = asosiasi antara aktor dan use case
- **Garis putus-putus dengan panah** = relasi antar use case (`«include»` = selalu dijalankan sebagai bagian dari use case lain; `«extend»` = dijalankan kondisional)

---

## 1. Fase Masukan

Aktor utama: **Admin LPPM** (pengelola utama data dasar), dengan kontribusi **Dosen/Peneliti** (memperbarui profil sendiri) dan **Ketua LPPM** (menyusun/menyetujui renstra).

*(Lihat gambar: Use Case Diagram — Fase Masukan)*

---

## 2. Fase Proses

Fase dengan aktor dan use case terbanyak. Aktor kiri (**Dosen/Peneliti**, **Mahasiswa**) berperan sebagai pengaju/pelaksana kegiatan; aktor kanan (**Reviewer**, **Ketua LPPM**, **Admin LPPM**) berperan sebagai penilai dan pengelola tata kelola. Relasi `«extend»` menunjukkan bahwa **Ajukan Klirens Etik** hanya dijalankan bila proposal berisiko etik; relasi `«include»` menunjukkan **Nilai Proposal** selalu menjadi bagian dari **Tetapkan Pemenang Hibah**, dan **Isi Logbook Monev** menjadi masukan wajib bagi **Lakukan Monev**.

*(Lihat gambar: Use Case Diagram — Fase Proses)*

---

## 3. Fase Luaran

Aktor utama: **Dosen/Peneliti** mencatat seluruh capaian (publikasi, HKI, produk), sementara **Admin LPPM** memverifikasi dan mengarsipkan laporan akhir & SPJ.

*(Lihat gambar: Use Case Diagram — Fase Luaran)*

---

## 4. Fase Dampak

Aktor terbanyak ragamnya: **Admin LPPM** mengelola data sitasi dan kerja sama; **Mitra Eksternal** mengisi survei dampak; **Ketua LPPM**, **Prodi/GKM**, dan **Pimpinan** mengakses dashboard analitik sebagai bahan evaluasi dan penyusunan LED/LKPS. Relasi `«include»` menunjukkan hasil **Isi Survei Dampak** menjadi salah satu sumber data **Lihat Dashboard Analitik** (bersama data sitasi, HKI, dan publikasi dari fase-fase sebelumnya).

*(Lihat gambar: Use Case Diagram — Fase Dampak)*

---

## Ringkasan Aktor Lintas Fase

| Aktor | Fase yang Diikuti | Peran Utama |
|---|---|---|
| **Dosen/Peneliti** | Proses, Luaran (+ Masukan) | Pengaju & pelaksana kegiatan, pencatat luaran |
| **Mahasiswa** | Proses | Anggota tim penelitian/PkM |
| **Reviewer** | Proses | Penilai proposal dan monev |
| **Admin LPPM** | Masukan, Proses, Luaran, Dampak | Pengelola operasional harian seluruh modul |
| **Ketua LPPM** | Masukan, Proses, Dampak | Pengambil keputusan & pemantau capaian |
| **Prodi/GKM** | Dampak | Pengguna data untuk penyusunan LED/LKPS |
| **Pimpinan** | Dampak | Pemantau tren capaian institusi |
| **Mitra Eksternal** | Dampak | Sumber bukti dampak nyata PkM |

Ringkasan ini menegaskan bahwa **Admin LPPM** adalah aktor dengan cakupan paling luas di seluruh fase — konsisten dengan rekomendasi RBAC pada model SIMPPM sebelumnya yang menempatkan peran ini sebagai pengelola inti sistem.
