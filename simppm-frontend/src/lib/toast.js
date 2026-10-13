// Notifikasi singkat (toast) untuk umpan balik aksi pengguna, mis. setelah
// data berhasil disimpan/dihapus. Ditampilkan oleh components/Toaster.svelte.

import { writable } from 'svelte/store';

/** @type {import('svelte/store').Writable<Array<{id: number, pesan: string, jenis: string}>>} */
export const daftarToast = writable([]);

let idBerikutnya = 1;

/** jenis: 'sukses' | 'gagal' | 'info' */
export function tampilkanToast(pesan, jenis = 'sukses', durasi = 3500) {
  const id = idBerikutnya++;
  daftarToast.update((list) => [...list, { id, pesan, jenis }]);
  setTimeout(() => tutupToast(id), durasi);
}

export function tutupToast(id) {
  daftarToast.update((list) => list.filter((t) => t.id !== id));
}
