# SIMPPM — Sistem Informasi Pengelolaan Penelitian dan Pengabdian kepada Masyarakat

Prototipe aplikasi manajemen siklus Penelitian dan Pengabdian kepada
Masyarakat (PPM) untuk perguruan tinggi, dibangun mengikuti prinsip
**12-factor app**, dengan backend dan frontend sebagai dua basis kode
yang **sepenuhnya terpisah (uncoupled)**, hanya berkomunikasi lewat
JSON:API.

## Struktur Proyek

```
simppm-backend/     Laravel 12 + SQLite, API JSON:API, RBAC, aturan bisnis, 173 test
simppm-frontend/    Svelte 5 + Vite, klien JSON:API murni, uncoupled dari backend
Kamus_Data_SIMPPM.md           Spesifikasi struktur data 27 entitas (sumber kebenaran)
Model_SIMPPM_LPPM.md           Model konseptual pengelolaan PPM
Use_Case_Diagram_SIMPPM.md     Use case 8 aktor/peran
*.png (erd_*, uc_*)            Diagram ERD & use case per fase siklus PPM
```

## Menjalankan Seluruh Aplikasi (Backend + Frontend)

**1. Backend** (jalankan lebih dulu, di terminal terpisah):

```bash
cd simppm-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --port=8123
```

Backend aktif di `http://127.0.0.1:8123/api/v1`.

**2. Frontend** (di terminal lain, setelah backend berjalan):

```bash
cd simppm-frontend
npm install
cp .env.example .env      # pastikan VITE_API_BASE_URL menunjuk ke backend di atas
npm run dev
```

Buka `http://localhost:5173` di browser, masuk dengan salah satu akun
demo (kata sandi `password`), misalnya `admin.lppm@simppm.test`.

## Verifikasi

```bash
# Test otomatis backend (173 test: JSON:API per entitas + aturan bisnis)
cd simppm-backend && php artisan test

# Build produksi frontend (aset statis, tanpa perlu backend berjalan)
cd simppm-frontend && npm run build
```

## Dokumentasi Detail

| Topik                                        | Lokasi                                                                         |
| -------------------------------------------- | ------------------------------------------------------------------------------ |
| Penerapan 12-factor app (lengkap per faktor) | [`simppm-backend/docs/12-FACTOR-APP.md`](simppm-backend/docs/12-FACTOR-APP.md) |
| Arsitektur & panduan backend                 | [`simppm-backend/README.md`](simppm-backend/README.md)                         |
| Arsitektur & panduan frontend                | [`simppm-frontend/README.md`](simppm-frontend/README.md)                       |
| Spesifikasi data 27 entitas                  | [`Kamus_Data_SIMPPM.md`](Kamus_Data_SIMPPM.md)                                 |
| Model pengelolaan PPM                        | [`Model_SIMPPM_LPPM.md`](Model_SIMPPM_LPPM.md)                                 |
| Use case 8 peran/aktor                       | [`Use_Case_Diagram_SIMPPM.md`](Use_Case_Diagram_SIMPPM.md)                     |

## Ringkasan Teknis

| Aspek             | Pilihan                                                               |
| ----------------- | --------------------------------------------------------------------- |
| Backend           | Laravel 12, PHP 8.2+                                                  |
| Basis data        | SQLite (portabel, file tunggal)                                       |
| Gaya API          | JSON:API (`laravel-json-api/laravel`)                                 |
| Autentikasi       | Laravel Sanctum (token, stateless)                                    |
| Otorisasi         | 27 Policy class (RBAC, 8 peran)                                       |
| Aturan bisnis     | 6 aturan kunci sebagai service + exception khusus                     |
| Pengujian backend | 173 test (feature + unit), seluruhnya lulus                           |
| Frontend          | Svelte 5 + Vite, `svelte-spa-router`, uncoupled dari backend          |
| Penamaan          | Konsisten Bahasa Indonesia di seluruh model, field, dan komentar kode |

# Lisensi

Perangkat lunak ini berada di bawah lisensi AGPL-3.0-or-later.
