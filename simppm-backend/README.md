# SIMPPM Backend

Backend Sistem Informasi Pengelolaan Penelitian dan Pengabdian kepada
Masyarakat (SIMPPM) — Laravel 12 + SQLite, API berbasis **JSON:API**,
dibangun sesuai prinsip *12-factor app* (lihat `docs/12-FACTOR-APP.md`
untuk pembahasan lengkap per faktor).

Backend ini sepenuhnya **stateless** dan **uncoupled** dari frontend —
frontend Svelte (`../simppm-frontend`) berkomunikasi murni lewat HTTP
JSON:API, tanpa berbagi kode atau proses.

## Ringkasan

- **27 entitas** mengikuti 4 fase siklus PPM (Masukan → Proses →
  Luaran/Capaian → Dampak), sesuai `Kamus_Data_SIMPPM.md` &
  `Model_SIMPPM_LPPM.md` di root proyek.
- **RBAC**: 27 Policy class untuk 8 peran/aktor (Admin LPPM, Ketua LPPM,
  Dosen/Peneliti, Mahasiswa, Reviewer, Prodi/GKM, Pimpinan, Mitra
  Eksternal) sesuai `Use_Case_Diagram_SIMPPM.md`.
- **6 aturan bisnis kunci** ditegakkan lewat `ValidasiAturanBisnisService`
  + *exception* khusus (`app/Exceptions/BisnisSimppm/*`), mis. klirens
  etik wajib sebelum kontrak, pencairan dana tidak boleh melebihi nilai
  kontrak, transisi status proposal yang valid, dst.
- **173 test otomatis** (feature test JSON:API per entitas + unit test
  aturan bisnis), seluruhnya lulus.
- Basis data **SQLite** file tunggal — portabel, tanpa server DB terpisah.

## Menjalankan Secara Lokal

Prasyarat: PHP 8.2+, Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # jika belum ada
php artisan migrate:fresh --seed
php artisan serve --port=8123
```

API akan tersedia di `http://127.0.0.1:8123/api/v1`.

## Menjalankan Test

```bash
php artisan test
```

Hasil yang diharapkan: **173 test lulus** (feature test JSON:API untuk
27 entitas + unit test 6 aturan bisnis kunci).

## Autentikasi

Autentikasi berbasis token (Laravel Sanctum, *personal access token*,
bukan sesi cookie — konsisten dengan sifat *stateless* backend):

```
POST /api/v1/auth/login   { "email": "...", "password": "..." }
→ { "data": { "token": "...", "user": { ... } } }

POST /api/v1/auth/logout  (header Authorization: Bearer <token>)
```

Seluruh endpoint resource JSON:API lain mewajibkan header
`Authorization: Bearer <token>` dan `Accept: application/vnd.api+json`.

### Akun Demo (kata sandi seragam: `password`)

| Peran | Email |
|---|---|
| Admin LPPM | admin.lppm@simppm.test |
| Ketua LPPM | ketua.lppm@simppm.test |
| Dosen/Peneliti | dosen@simppm.test |
| Mahasiswa | mahasiswa@simppm.test |
| Reviewer | reviewer@simppm.test |
| Prodi/GKM | prodi.gkm@simppm.test |
| Pimpinan | pimpinan@simppm.test |
| Mitra Eksternal | mitra@simppm.test |

Seeder (`database/seeders/DatabaseSeeder.php`) juga menyediakan data
rujukan minimal (Program Studi, Renstra, Skema Pendanaan, Pedoman, Sarana
Prasarana) agar seluruh alur pembuatan data lewat frontend — termasuk
entitas yang mewajibkan relasi ke data rujukan tersebut — dapat langsung
dicoba tanpa harus mengisi data induk secara manual terlebih dahulu.

## Skrip Generator (spec-driven)

Seluruh migration, model, factory, schema JSON:API, policy, dan metadata
frontend **dibangkitkan otomatis** dari satu sumber kebenaran,
`spec/entities.json`, agar konsisten dan mudah diperbarui bila struktur
data berubah:

```bash
python3 spec/generate.py             # migrations + models + factories
python3 spec/generate_jsonapi.py     # Schema, Query, Request JSON:API
python3 spec/generate_policies.py    # 27 Policy class (RBAC)
python3 spec/generate_frontend_meta.py  # entities.meta.js untuk frontend Svelte
php artisan migrate:fresh --seed --force
```

## Struktur Proyek (ringkas)

```
app/
├─ Models/                  # 27 model Eloquent + User (relasi & ownership)
├─ Policies/                # 27 Policy class (RBAC per peran)
├─ JsonApi/V1/<Entitas>/     # Schema, Query, Request per entitas (JSON:API)
├─ Services/
│  └─ ValidasiAturanBisnisService.php  # 6 aturan bisnis kunci
├─ Exceptions/BisnisSimppm/  # Exception khusus per aturan bisnis
└─ Http/Controllers/Auth/    # Login/logout (Sanctum)
database/
├─ migrations/               # 27 tabel + personal_access_tokens + kolom peran user
├─ factories/                # Factory tiap entitas (data uji realistis, locale id_ID)
└─ seeders/DatabaseSeeder.php
docs/
└─ 12-FACTOR-APP.md          # Dokumentasi penerapan 12-factor app (lengkap per faktor)
spec/
├─ entities.json              # Sumber kebenaran struktur 27 entitas
└─ generate*.py               # Generator kode dari spec di atas
tests/
├─ Feature/                   # Test endpoint JSON:API per entitas (27 entitas)
└─ Unit/                      # Test aturan bisnis kunci
```

## Dokumentasi Lengkap

- [`docs/12-FACTOR-APP.md`](docs/12-FACTOR-APP.md) — penerapan seluruh 12
  faktor (Codebase, Config, Backing services, Build-Release-Run,
  Processes, Port binding, Concurrency, Disposability, Dev/prod parity,
  Logs, Admin processes), termasuk bagian frontend Svelte.
- [`../simppm-frontend/README.md`](../simppm-frontend/README.md) —
  dokumentasi frontend (arsitektur, cara jalan, konvensi kode).
- `Kamus_Data_SIMPPM.md`, `Model_SIMPPM_LPPM.md`,
  `Use_Case_Diagram_SIMPPM.md` (root proyek) — spesifikasi sumber yang
  menjadi basis seluruh entitas, relasi, dan RBAC di atas.
