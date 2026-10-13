// Tema tampilan (terang/gelap) daisyUI. Nilai awal sudah diterapkan oleh
// skrip kecil di index.html sebelum aplikasi dimuat (mencegah kedipan);
// store ini menyinkronkan pilihan pengguna ke atribut data-theme & storage.

import { writable } from 'svelte/store';

const STORAGE_KEY = 'simppm.tema';
export const TEMA_TERANG = 'simppm';
export const TEMA_GELAP = 'simppm-gelap';

export const tema = writable(document.documentElement.getAttribute('data-theme') || TEMA_TERANG);

tema.subscribe((nilai) => {
  document.documentElement.setAttribute('data-theme', nilai);
});

export function gantiTema() {
  tema.update((nilai) => {
    const baru = nilai === TEMA_GELAP ? TEMA_TERANG : TEMA_GELAP;
    try {
      localStorage.setItem(STORAGE_KEY, baru);
    } catch {
      // Storage tidak tersedia (mode privat) -- tema tetap berlaku sesi ini.
    }
    return baru;
  });
}
