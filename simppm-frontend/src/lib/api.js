// Klien HTTP tipis untuk berkomunikasi dengan backend JSON:API SIMPPM.
//
// Backend bersifat sepenuhnya stateless (autentikasi token Sanctum, lihat
// auth.js) sehingga klien ini hanya perlu mengirim header `Authorization:
// Bearer <token>` pada setiap permintaan -- tidak ada cookie/sesi server
// yang harus dikelola di sisi frontend.

import { getToken, clearSession } from './auth.js';

const BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8123/api/v1').replace(/\/+$/, '');

const JSON_API_HEADERS = {
  Accept: 'application/vnd.api+json',
  'Content-Type': 'application/vnd.api+json',
};

/**
 * Merepresentasikan kegagalan permintaan API, membawa daftar error
 * berformat JSON:API (array `errors`) agar dapat ditampilkan apa adanya
 * ke pengguna (mis. pesan validasi atau pelanggaran aturan bisnis).
 */
export class ApiError extends Error {
  constructor(status, errors) {
    const pesanUtama = errors?.[0]?.detail || errors?.[0]?.title || `Permintaan gagal (HTTP ${status})`;
    super(pesanUtama);
    this.status = status;
    this.errors = errors || [];
  }
}

function buildUrl(path, params) {
  const url = new URL(BASE_URL + path);
  if (params) {
    for (const [key, value] of Object.entries(params)) {
      if (value === undefined || value === null || value === '') continue;
      url.searchParams.set(key, value);
    }
  }
  return url.toString();
}

async function request(method, path, { params, body } = {}) {
  const token = getToken();
  const headers = { ...JSON_API_HEADERS };
  if (token) headers.Authorization = `Bearer ${token}`;

  const response = await fetch(buildUrl(path, params), {
    method,
    headers,
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });

  // Token kedaluwarsa/dicabut -- bersihkan sesi lokal agar pengguna
  // diarahkan kembali ke halaman login oleh penjaga rute (route guard).
  if (response.status === 401) {
    clearSession();
  }

  if (response.status === 204) return null;

  const text = await response.text();
  const payload = text ? JSON.parse(text) : null;

  if (!response.ok) {
    throw new ApiError(response.status, payload?.errors);
  }

  return payload;
}

/**
 * Ambil daftar resource (dengan pagination & filter opsional).
 * `include`: daftar nama field relasi (toOne) untuk disertakan sekaligus
 * (lewat query `include` JSON:API) sehingga label relasi (mis. nama prodi
 * pada baris dosen) bisa ditampilkan tanpa permintaan tambahan per baris.
 */
export function listResource(resourceType, { page = 1, pageSize = 15, filters = {}, include = [] } = {}) {
  const params = {
    'page[number]': page,
    'page[size]': pageSize,
  };
  if (include.length) params.include = include.join(',');
  for (const [field, value] of Object.entries(filters)) {
    if (value !== '' && value !== undefined && value !== null) {
      params[`filter[${field}]`] = value;
    }
  }
  return request('GET', `/${resourceType}`, { params });
}

/**
 * Ambil satu resource berdasarkan ID.
 * `include`: daftar field relasi yang perlu disertakan -- backend JSON:API
 * ini HANYA menyertakan resource linkage (`relationships.<field>.data`)
 * untuk field yang diminta lewat `include`; tanpa itu, objek relationships
 * hanya berisi `links` tanpa `data.id` (dikonfirmasi lewat pengujian
 * langsung ke backend). Form Ubah wajib memakai ini agar dropdown relasi
 * bisa terisi dengan nilai yang tersimpan.
 */
export function getResource(resourceType, id, { include = [] } = {}) {
  const params = include.length ? { include: include.join(',') } : undefined;
  return request('GET', `/${resourceType}/${id}`, { params });
}

/** Buat resource baru. `attributes` & `relationships` mengikuti format JSON:API. */
export function createResource(resourceType, attributes, relationships) {
  const data = { type: resourceType, attributes };
  if (relationships && Object.keys(relationships).length) data.relationships = relationships;
  return request('POST', `/${resourceType}`, { body: { data } });
}

/** Perbarui resource yang sudah ada. */
export function updateResource(resourceType, id, attributes, relationships) {
  const data = { type: resourceType, id: String(id), attributes };
  if (relationships && Object.keys(relationships).length) data.relationships = relationships;
  return request('PATCH', `/${resourceType}/${id}`, { body: { data } });
}

/** Hapus resource. */
export function deleteResource(resourceType, id) {
  return request('DELETE', `/${resourceType}/${id}`);
}

export { BASE_URL };
