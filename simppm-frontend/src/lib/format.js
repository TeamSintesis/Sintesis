// Util pemformatan tampilan (label, tanggal, angka) yang dipakai bersama
// oleh komponen generik ResourceList & ResourceForm.

import { findEntity } from './entities.meta.js';

/** Format angka desimal sebagai mata uang Rupiah untuk tampilan tabel. */
export function formatRupiah(value) {
  if (value === null || value === undefined || value === '') return '-';
  const angka = Number(value);
  if (Number.isNaN(angka)) return String(value);
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(angka);
}

/** Format tanggal ISO menjadi format tanggal Indonesia yang ringkas. */
export function formatTanggal(value) {
  if (!value) return '-';
  const tanggal = new Date(value);
  if (Number.isNaN(tanggal.getTime())) return String(value);
  return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(tanggal);
}

/** Nilai tampilan generik untuk sel tabel berdasarkan tipe kolom metadata. */
export function formatNilaiTabel(attr, value) {
  if (value === null || value === undefined || value === '') return '-';
  switch (attr.type) {
    case 'boolean':
      return value ? 'Ya' : 'Tidak';
    case 'date':
      return formatTanggal(value);
    case 'decimal':
      return typeof value === 'string' || typeof value === 'number' ? Number(value).toLocaleString('id-ID') : value;
    default:
      return String(value);
  }
}

/**
 * Pilih kolom string paling representatif pada metadata entitas untuk
 * dipakai sebagai label tampilan pada dropdown relasi & tabel (mis.
 * menampilkan "Dr. Budi Santoso" bukan NIDN saat memilih dosen pengusul
 * pada form Proposal). Diprioritaskan nama kolom yang lazim berisi nama
 * atau judul; bila tidak ada, jatuh ke kolom string pertama.
 */
export function kolomLabelUtama(entityMeta) {
  const kandidatAwalan = entityMeta.attributes.find(
    (a) => a.type === 'string' && /^(nama|judul|title|label)/i.test(a.name)
  );
  if (kandidatAwalan) return kandidatAwalan.field;
  const kandidatLain = entityMeta.attributes.find((a) => a.type === 'string');
  return kandidatLain ? kandidatLain.field : 'id';
}

/** Potong teks panjang untuk tampilan sel tabel, tambahkan elipsis. */
export function potongTeks(teks, panjangMaks = 60) {
  if (typeof teks !== 'string') return teks;
  return teks.length > panjangMaks ? teks.slice(0, panjangMaks - 1) + '…' : teks;
}

/**
 * Bangun lookup `"type:id" -> attributes` dari array `included` pada
 * respons JSON:API (hasil query `include=...`), untuk menampilkan label
 * relasi (mis. nama prodi) di tabel daftar tanpa permintaan tambahan.
 */
export function bangunLookupIncluded(payload) {
  const lookup = {};
  for (const item of payload?.included ?? []) {
    lookup[`${item.type}:${item.id}`] = item.attributes ?? {};
  }
  return lookup;
}

/**
 * Ambil label tampilan untuk satu relasi toOne pada sebuah baris resource,
 * memakai lookup `included` dan metadata entitas terkait untuk menentukan
 * kolom label utamanya (lihat kolomLabelUtama).
 */
export function labelRelasi(row, relasi, includedLookup) {
  const ref = row.relationships?.[relasi.field]?.data;
  if (!ref) return '-';
  const atribut = includedLookup[`${ref.type}:${ref.id}`];
  if (!atribut) return `#${ref.id}`;
  const entitasTerkait = findEntity(ref.type);
  const kolom = entitasTerkait ? kolomLabelUtama(entitasTerkait) : null;
  return kolom && atribut[kolom] ? atribut[kolom] : `#${ref.id}`;
}
