// Metadata tampilan (ikon, warna, deskripsi) yang dipakai bersama oleh
// Sidebar, Dashboard, ResourceList, dan ResourceForm. Kelas Tailwind
// ditulis utuh (bukan dirangkai) agar terdeteksi oleh pemindai Tailwind.

import { findEntity } from './entities.meta.js';

/** Ikon, warna, dan tujuan setiap fase siklus PPM (lihat Model_SIMPPM_LPPM.md). */
export const FASE_UI = {
  masukan: {
    ikon: 'inbox',
    teks: 'text-info',
    latar: 'bg-info/10',
    badge: 'badge-info',
    garis: 'border-info',
    deskripsi: 'Fondasi kelembagaan: renstra, peta jalan, pedoman, sarana, pendanaan, dan mitra.',
  },
  proses: {
    ikon: 'clipboard-check',
    teks: 'text-warning',
    latar: 'bg-warning/15',
    badge: 'badge-warning',
    garis: 'border-warning',
    deskripsi: 'Tata kelola pelaksanaan: pengusulan, review, klirens etik, kontrak, dan monev.',
  },
  luaran: {
    ikon: 'award',
    teks: 'text-success',
    latar: 'bg-success/10',
    badge: 'badge-success',
    garis: 'border-success',
    deskripsi: 'Bukti capaian terukur: publikasi, kekayaan intelektual, produk, dan laporan.',
  },
  dampak: {
    ikon: 'trending-up',
    teks: 'text-secondary',
    latar: 'bg-secondary/10',
    badge: 'badge-secondary',
    garis: 'border-secondary',
    deskripsi: 'Bukti relevansi nyata: sitasi, rekognisi, kerja sama, dan survei dampak.',
  },
};

// Kode enum yang ditulis khusus (singkatan/nama diri); sisanya cukup
// diubah dari snake_case menjadi kalimat biasa.
const LABEL_ENUM_KHUSUS = {
  pkm: 'PkM',
  pt_lain: 'PT lain',
  pemda: 'Pemda',
  scopus: 'Scopus',
  sinta: 'SINTA',
  google_scholar: 'Google Scholar',
  nasional_non_sinta: 'Nasional non-SINTA',
  hak_cipta: 'Hak cipta',
};

/** Label tampilan untuk nilai enum, mis. "sinta_2" -> "SINTA 2". */
export function labelEnum(nilai) {
  if (nilai === null || nilai === undefined || nilai === '') return '-';
  const kode = String(nilai);
  if (LABEL_ENUM_KHUSUS[kode]) return LABEL_ENUM_KHUSUS[kode];
  if (/^sinta_\d$/.test(kode)) return `SINTA ${kode.slice(-1)}`;
  const teks = kode.replace(/_/g, ' ');
  return teks.charAt(0).toUpperCase() + teks.slice(1);
}

const STATUS_SUKSES = ['disetujui', 'terbit', 'selesai', 'dicairkan', 'aktif', 'terverifikasi', 'tersedia'];
const STATUS_GAGAL = ['ditolak', 'rusak', 'berakhir'];
const STATUS_PROSES = ['diajukan', 'direview', 'ditinjau', 'diperiksa_substantif', 'digunakan', 'diperpanjang'];
const STATUS_TUNGGU = ['dijadwalkan', 'terjadwal', 'tertunda', 'belum'];

/** Kelas badge daisyUI untuk nilai enum status (netral bila bukan status). */
export function kelasBadgeEnum(nilai) {
  if (STATUS_SUKSES.includes(nilai)) return 'badge-success badge-soft';
  if (STATUS_GAGAL.includes(nilai)) return 'badge-error badge-soft';
  if (STATUS_PROSES.includes(nilai)) return 'badge-info badge-soft';
  if (STATUS_TUNGGU.includes(nilai)) return 'badge-warning badge-soft';
  return 'badge-ghost';
}

/** Kolom desimal yang bermakna nominal uang (ditampilkan dalam Rupiah). */
export function apakahUang(atribut) {
  return atribut.type === 'decimal' && /(dana|rab|nilai|jumlah|plafon)/i.test(atribut.name);
}

/** "skemaPendanaan" -> "Skema pendanaan" untuk label kolom/field relasi. */
export function labelField(field) {
  const teks = field.replace(/([a-z0-9])([A-Z])/g, '$1 $2').toLowerCase();
  return teks.charAt(0).toUpperCase() + teks.slice(1);
}

/** Label entitas tujuan sebuah relasi, mis. relasi "pengusul" -> "Dosen/Peneliti". */
export function labelEntitasRelasi(relasi) {
  return findEntity(relasi.resourceType)?.label ?? relasi.resourceType;
}

/** Inisial nama untuk avatar, mis. "Budi Santoso" -> "BS". */
export function inisial(nama) {
  if (!nama) return 'U';
  const kata = nama.trim().split(/\s+/);
  if (kata.length === 1) return kata[0][0].toUpperCase();
  return (kata[0][0] + kata[kata.length - 1][0]).toUpperCase();
}

/** Salam sesuai jam lokal. */
export function salam() {
  const jam = new Date().getHours();
  if (jam >= 3 && jam < 11) return 'Selamat pagi';
  if (jam >= 11 && jam < 15) return 'Selamat siang';
  if (jam >= 15 && jam < 18) return 'Selamat sore';
  return 'Selamat malam';
}
