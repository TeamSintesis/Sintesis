// Pengelolaan sesi autentikasi di sisi frontend.
//
// Backend memakai Laravel Sanctum dengan token bearer (BUKAN sesi cookie
// server-side) -- lihat komentar pada AuthController@login di backend.
// Frontend menyimpan token di localStorage dan mengirimkannya lewat header
// Authorization pada setiap permintaan (lib/api.js). Ini konsisten dengan
// prinsip 12-factor app "Processes" (VI): tidak ada state sesi yang
// dibagikan antar-proses server; seluruh state sesi berada di sisi klien.

import { writable, get } from 'svelte/store';

const STORAGE_KEY = 'simppm.session';

// Endpoint auth (login/logout/me) BUKAN resource JSON:API biasa -- lihat
// AuthController di backend -- sehingga memakai `Content-Type: application/json`
// polos, bukan `application/vnd.api+json` seperti lib/api.js.
const AUTH_BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8123/api/v1').replace(/\/+$/, '');

function readStoredSession() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

/** Store reaktif berisi { token, user } atau null bila belum login. */
export const session = writable(readStoredSession());

export function getToken() {
  return get(session)?.token ?? null;
}

export function getUser() {
  return get(session)?.user ?? null;
}

export function isAuthenticated() {
  return get(session) !== null;
}

function persist(value) {
  if (value) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(value));
  } else {
    localStorage.removeItem(STORAGE_KEY);
  }
}

export function setSession(token, user) {
  const value = { token, user };
  persist(value);
  session.set(value);
}

export function clearSession() {
  persist(null);
  session.set(null);
}

/**
 * Login ke backend (POST /auth/login) dan menyimpan token + data pengguna
 * ke sesi lokal bila berhasil. Melempar Error dengan pesan dari backend
 * bila kredensial salah (401) atau input tidak valid (422).
 */
export async function login(email, password) {
  const response = await fetch(`${AUTH_BASE_URL}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ email, password }),
  });

  const text = await response.text();
  const payload = text ? JSON.parse(text) : null;

  if (!response.ok) {
    const pesan = payload?.message || payload?.errors?.email?.[0] || 'Email atau kata sandi salah.';
    throw new Error(pesan);
  }

  setSession(payload.data.token, payload.data.user);
  return payload.data.user;
}

/**
 * Logout ke backend (POST /auth/logout) untuk mencabut token Sanctum saat
 * ini, lalu membersihkan sesi lokal apa pun hasilnya (agar pengguna selalu
 * bisa keluar walau permintaan ke server gagal, mis. token sudah expired).
 */
export async function logout() {
  const token = getToken();
  try {
    if (token) {
      await fetch(`${AUTH_BASE_URL}/auth/logout`, {
        method: 'POST',
        headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
      });
    }
  } catch {
    // Diabaikan -- sesi lokal tetap dibersihkan di bawah.
  } finally {
    clearSession();
  }
}

/**
 * Label tampilan yang mudah dibaca untuk setiap kode peran (RBAC),
 * ditampilkan di header aplikasi dan dipakai untuk pengelompokan menu.
 */
export const PERAN_LABELS = {
  admin_lppm: 'Admin LPPM',
  ketua_lppm: 'Ketua LPPM',
  dosen: 'Dosen/Peneliti',
  mahasiswa: 'Mahasiswa',
  reviewer: 'Reviewer',
  prodi_gkm: 'Prodi/GKM',
  pimpinan: 'Pimpinan',
  mitra_eksternal: 'Mitra Eksternal',
};
