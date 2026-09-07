# Penerapan Prinsip 12-Factor App pada SIMPPM Backend

Dokumen ini menjelaskan bagaimana backend **SIMPPM** (Sistem Informasi
Pengelolaan Penelitian dan Pengabdian kepada Masyarakat) — dibangun dengan
Laravel 12, database SQLite, dan API berformat JSON:API — menerapkan
[12-Factor App](https://12factor.net/) sebagai metodologi pengembangan
aplikasi *software-as-a-service* modern. Setiap bagian memuat: **prinsip**,
**implementasi konkret di proyek ini** (dengan rujukan berkas), dan
**status/catatan** untuk pengembangan lanjutan (termasuk saat frontend
Svelte yang uncoupled dibangun).

Ringkasan kepatuhan:

| # | Faktor | Status |
|---|--------|--------|
| I | Codebase | ✅ Diterapkan |
| II | Dependencies | ✅ Diterapkan |
| III | Config | ✅ Diterapkan |
| IV | Backing Services | ✅ Diterapkan |
| V | Build, Release, Run | ✅ Diterapkan |
| VI | Processes | ✅ Diterapkan |
| VII | Port Binding | ✅ Diterapkan |
| VIII | Concurrency | ✅ Diterapkan (siap skala horizontal) |
| IX | Disposability | ✅ Diterapkan |
| X | Dev/Prod Parity | ✅ Diterapkan |
| XI | Logs | ✅ Diterapkan |
| XII | Admin Processes | ✅ Diterapkan |

---

## I. Codebase

> Satu basis kode yang dilacak dalam sistem kontrol versi, dengan banyak
> *deploy* (dev, staging, production) yang berasal dari basis kode yang sama.

**Implementasi:**

- Backend (`simppm-backend/`, Laravel) dan frontend (Svelte, terpisah/
  *uncoupled*) sengaja dipisah menjadi **dua basis kode independen** yang
  berkomunikasi murni lewat HTTP/JSON:API — bukan satu *monorepo* yang
  saling menyisipkan proses satu sama lain. Ini konsisten dengan prinsip
  Codebase (satu basis kode = satu aplikasi/*deploy unit*) sekaligus
  memenuhi syarat eksplisit "frontend harus uncoupled" dari kebutuhan
  proyek.
- Struktur proyek Laravel standar (`app/`, `config/`, `database/`,
  `routes/`, `tests/`) sepenuhnya dihasilkan dari satu sumber definisi
  entitas (`spec/entities.json`) melalui skrip generator
  (`spec/generate.py`, `spec/generate_jsonapi.py`,
  `spec/generate_policies.py`, `spec/generate_tests.py`) — sehingga
  migrations, model, factory, schema JSON:API, policy, dan test *feature*
  untuk ke-27 entitas selalu konsisten dan dapat diregenerasi ulang dari
  satu sumber kebenaran (*single source of truth*), bukan ditulis manual
  berulang kali dengan risiko drift antar-lingkungan.
- `.gitignore` sudah menyingkirkan berkas yang tidak boleh masuk kontrol
  versi (`.env`, `vendor/`, `node_modules/`, `storage/*.key`, cache
  bootstrap, dll).

**Status:** Diterapkan. Rekomendasi saat rilis: inisialisasi git repository
terpisah untuk backend dan frontend (atau dua folder dalam satu monorepo
dengan *deploy pipeline* independen), dengan tag/rilis versi mengikuti
[SemVer](https://semver.org/).

---

## II. Dependencies

> Secara eksplisit mendeklarasikan dan mengisolasi dependensi — jangan
> pernah bergantung pada asumsi adanya paket sistem tersirat.

**Implementasi:**

- Seluruh dependensi PHP dideklarasikan secara eksplisit di
  `composer.json` dan dikunci versi persisnya di `composer.lock`:
  - `php: ^8.2` (lingkungan pengembangan memakai PHP 8.5.4)
  - `laravel/framework: ^12.0` (terpasang v12.68.0)
  - `laravel-json-api/laravel: ^5.3` (terpasang v5.3.0) — mesin JSON:API
  - `laravel/sanctum: ^4.0` (terpasang v4.3.3) — autentikasi token untuk
    SPA yang uncoupled
  - `laravel/tinker: ^2.10.1`
  - Dependensi pengembangan (`require-dev`): `phpunit/phpunit`,
    `fakerphp/faker`, `laravel/pint`, `mockery/mockery`, dll — terisolasi
    dari dependensi produksi lewat `composer install --no-dev` saat rilis.
- Tidak ada kode yang mengasumsikan biner/paket sistem tersirat (mis.
  memanggil `curl` binary langsung, atau bergantung pada ekstensi PHP yang
  tidak dinyatakan). Driver database (`pdo_sqlite`) adalah ekstensi inti
  PHP yang dapat diverifikasi lewat `composer.json` -> `require` platform
  checks bawaan Composer.
- Frontend Svelte (basis kode terpisah) akan mendeklarasikan dependensinya
  sendiri di `package.json` miliknya sendiri — terisolasi total dari
  dependensi Composer backend, tidak berbagi `node_modules` atau
  `vendor/`.

**Status:** Diterapkan. Instalasi bersih hanya membutuhkan
`composer install` (backend) dan `npm install` (frontend) — tidak ada
langkah manual "install paket X ke sistem" yang didokumentasikan di luar
manajer paket.

---

## III. Config

> Simpan konfigurasi pada *environment* (variabel lingkungan), bukan
> tertanam (hard-coded) di dalam kode sumber. Konfigurasi harus berbeda
> antar-*deploy* (dev/staging/production); kode harus sama.

**Implementasi:**

- Semua nilai yang berbeda antar-lingkungan dibaca lewat helper `env()`
  Laravel dan didefinisikan di berkas `.env` (tidak dikomit ke kontrol
  versi — lihat `.gitignore`), dengan `.env.example` sebagai templat tanpa
  nilai rahasia:
  - Kredensial & identitas aplikasi: `APP_KEY`, `APP_ENV`, `APP_DEBUG`,
    `APP_URL`
  - Koneksi basis data: `DB_CONNECTION=sqlite`, `DB_DATABASE`
  - Locale: `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE=id_ID`
  - Driver session/cache/queue: `SESSION_DRIVER`, `CACHE_STORE`,
    `QUEUE_CONNECTION`
  - Logging: `LOG_CHANNEL`, `LOG_LEVEL`
- **Konfigurasi khusus domain SIMPPM** dipisah ke berkas konfigurasi
  tersendiri, `config/simppm.php`, alih-alih ditanam sebagai angka ajaib
  (*magic number*) di dalam kode aturan bisnis:

  ```php
  'ambang_batas_skor_penilaian' => (float) env('SIMPPM_AMBANG_BATAS_SKOR', 70.0),
  ```

  Nilai ini dikonsumsi oleh `App\Services\ValidasiAturanBisnisService`
  melalui `config('simppm.ambang_batas_skor_penilaian')` — sehingga ambang
  batas kelulusan skor Penilaian proposal dapat diubah per-lingkungan
  (mis. dinaikkan di production, diturunkan untuk staging/demo) **tanpa
  mengubah satu baris kode pun**, cukup mengatur `SIMPPM_AMBANG_BATAS_SKOR`
  di `.env`. Ini terverifikasi lewat test
  `ambang batas skor dapat dikonfigurasi` di
  `tests/Unit/ValidasiAturanBisnisServiceTest.php`.
- Tidak ada kredensial, *connection string*, atau nilai spesifik
  lingkungan yang tertanam langsung di kode PHP — seluruhnya melalui
  `config/*.php` yang membaca `env()`, sesuai konvensi Laravel.
- Frontend Svelte akan memakai variabel `VITE_*`/`.env` miliknya sendiri
  (mis. `VITE_API_BASE_URL`) untuk menunjuk ke alamat backend API sesuai
  lingkungan (dev/staging/production) tanpa mengubah kode sumber.

**Status:** Diterapkan.

---

## IV. Backing Services

> Perlakukan *backing services* (basis data, cache, antrian, penyimpanan
> berkas, dll.) sebagai *resource* terlampir yang dapat ditukar melalui
> konfigurasi, tanpa membedakan *resource* lokal vs pihak ketiga.

**Implementasi:**

- **Basis data** — dipilih SQLite (`database/database.sqlite`) demi
  portabilitas sesuai kebutuhan proyek, tetapi diakses murni lewat
  Eloquent ORM/Query Builder Laravel, bukan query SQL spesifik-dialek yang
  menyulitkan migrasi. Karena koneksi ditentukan oleh
  `DB_CONNECTION=sqlite` di `.env` dan konfigurasi lengkapnya
  (`config/database.php`) sudah menyediakan definisi siap pakai untuk
  MySQL, PostgreSQL, dan SQL Server, backend ini dapat **beralih ke basis
  data server (mis. PostgreSQL) di production hanya dengan mengganti
  variabel environment**, tanpa mengubah kode aplikasi.
- **Cache** — `CACHE_STORE=database` (memakai tabel `cache` di basis data
  yang sama) untuk kesederhanaan pengembangan; dapat ditukar ke `redis`
  atau `memcached` di production hanya via `.env`, karena driver-driver
  itu sudah terdaftar di `config/cache.php`.
- **Queue (antrian)** — `QUEUE_CONNECTION=database` untuk pekerjaan
  asinkron (mis. pengiriman notifikasi, ekspor laporan volume besar di
  masa depan); dapat ditukar ke `redis`/`sqs` tanpa mengubah kode *job*
  itu sendiri.
- **Session** — `SESSION_DRIVER=database`, hanya relevan untuk sesi web
  admin (jika ada); **API JSON:API sendiri bersifat stateless** dan tidak
  memakai session sama sekali (lihat Faktor VI).
- **Autentikasi API** — Laravel Sanctum diperlakukan sebagai *backing
  service* autentikasi token yang dapat dicabut/diaudit lewat tabel
  `personal_access_tokens`, dikonfigurasi lewat `config/sanctum.php` dan
  `.env` (`SANCTUM_STATEFUL_DOMAINS`, dll).
- **Penyimpanan berkas** — `FILESYSTEM_DISK=local`, siap ditukar ke `s3`
  (kredensial `AWS_*` sudah ada di `.env.example`) bila dokumen unggahan
  (mis. `dokumen_url` pada SPJ, laporan akhir) dipindah ke object storage
  di production.

**Status:** Diterapkan. Seluruh *backing service* ditentukan melalui
*resource handle* di `.env`, tidak ada *hardcoded* host/kredensial.

---

## V. Build, Release, Run

> Pisahkan tegas tahap *build* (kompilasi kode+dependensi jadi *build*),
> *release* (gabungkan *build* dengan config lingkungan tertentu), dan
> *run* (jalankan proses di lingkungan eksekusi). Setiap rilis harus punya
> ID unik dan tidak dapat diubah (immutable) setelah dibuat.

**Implementasi & alur yang direkomendasikan:**

1. **Build** — `composer install --no-dev --optimize-autoloader` (backend)
   menghasilkan `vendor/` dan *autoloader* teroptimasi; `npm run build`
   (frontend, terpisah) menghasilkan berkas statis terkompilasi. Tahap ini
   tidak menyentuh konfigurasi lingkungan apa pun.
2. **Release** — Hasil *build* digabung dengan `.env` spesifik lingkungan
   (staging/production), lalu dijalankan langkah rilis yang **idempoten**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan migrate --force
   ```
   Skrip regenerasi proyek ini sendiri (`spec/generate.py` →
   `generate_jsonapi.py` → `generate_policies.py` →
   `migrate:fresh --force`) mendemonstrasikan pola build/release yang
   sama: kode dihasilkan ulang secara deterministik dari `entities.json`,
   lalu skema basis data disinkronkan lewat migrasi bernomor.
3. **Run** — Proses aplikasi dijalankan tanpa melakukan perubahan kode
   atau konfigurasi runtime (mis. `php artisan serve --port=8123` untuk
   pengembangan/demo, atau PHP-FPM + Nginx/Caddy di production). Proses
   *run* murni mengeksekusi apa yang sudah dirilis, sesuai isolasi
   build/release/run.

**Status:** Diterapkan (Laravel secara inheren memisahkan ketiga tahap ini
lewat `artisan`); rekomendasi produksi: gunakan *pipeline* CI/CD (mis.
GitHub Actions) yang menghasilkan artefak *build* bertanda versi/commit
hash sebagai ID rilis, agar setiap rilis dapat ditelusuri dan di-*rollback*.

---

## VI. Processes

> Jalankan aplikasi sebagai satu atau lebih proses *stateless* yang tidak
> membagikan apa pun (*share-nothing*); simpan data yang perlu persisten
> ke *backing service* dengan status (basis data), bukan di memori/disk
> lokal proses.

**Implementasi:**

- **API stateless murni** — endpoint JSON:API (`routes/api.php`, prefix
  `v1/*`) memakai *middleware* `auth:sanctum` berbasis **token bearer**,
  bukan *cookie session* server-side. Setiap permintaan membawa token
  sendiri (`Authorization: Bearer <token>`), sehingga proses backend mana
  pun (di *server*/*container* mana pun) dapat melayani permintaan itu
  tanpa perlu *state* lokal — lihat `app/Http/Controllers/Auth/AuthController.php`:

  ```php
  // Frontend Svelte yang uncoupled berkomunikasi dengan backend sepenuhnya
  // melalui token bearer (stateless), sejalan dengan prinsip 12-factor app
  // "Processes" (VI) — proses backend tidak menyimpan sesi di memori/disk.
  ```
- Token dibuat lewat Sanctum (`$user->createToken(...)`) dan disimpan di
  tabel basis data `personal_access_tokens` — **bukan** di memori proses
  PHP atau berkas sesi lokal — sehingga token tetap valid apa pun proses
  PHP-FPM/worker yang menerima permintaan berikutnya.
- **Tidak ada state di memori antar-permintaan** — `ValidasiAturanBisnisService`
  (kelas layanan aturan bisnis) bersifat *stateless*: tidak memiliki
  properti instan yang menyimpan data antar-panggilan, hanya menerima
  input via parameter method dan bergantung pada `config()` (dibaca ulang
  tiap kali, bukan disimpan sebagai *field*).
- **Tidak ada penyimpanan berkas lokal sebagai sumber kebenaran** — data
  transaksional (Proposal, Kontrak, SPJ, Penilaian, dll.) seluruhnya
  disimpan di basis data (*backing service*, Faktor IV), bukan di
  filesystem lokal proses. File upload (bila ada di masa depan) mengarah
  ke *disk* abstraksi Laravel (`FILESYSTEM_DISK`), yang dapat berupa
  *object storage* bersama antar-proses.
- Uji otomatis (`tests/Feature/*.php`, `tests/Unit/*.php`) memvalidasi
  bahwa setiap permintaan tanpa token ditolak (`401`) dan setiap
  permintaan diperlakukan independen — konsisten dengan model *stateless
  request/response*.

**Status:** Diterapkan.

---

## VII. Port Binding

> Aplikasi harus *self-contained* dan mengekspos layanannya lewat
> *binding* pada satu port, tanpa bergantung pada *web server* eksternal
> yang disuntikkan saat *runtime*.

**Implementasi:**

- Laravel secara inheren *self-contained*: `public/index.php` adalah
  *front controller* tunggal yang menangani seluruh siklus
  *request → response*, dan aplikasi dapat langsung mem-bind port lewat
  server bawaan PHP:
  ```bash
  php artisan serve --port=8123
  ```
  (dipakai sepanjang pengembangan proyek ini via
  `pplx-tool start_server`). Ini membuktikan aplikasi berjalan sebagai
  layanan HTTP yang dapat diekspos pada port berapa pun tanpa konfigurasi
  eksternal tambahan.
- Di production, port binding cukup diserahkan ke *reverse proxy*
  (Nginx/Caddy) atau PHP-FPM yang meneruskan ke port yang sama — aplikasi
  sendiri tidak mengasumsikan keberadaan *web server* tertentu; ia hanya
  butuh *entry point* HTTP standar (PHP-FPM/`php artisan serve`/
  `octane`), yang berarti backend ini bisa diekspos sebagai layanan HTTP
  ke layanan lain (termasuk ke frontend Svelte yang uncoupled) hanya
  lewat URL+port, tanpa *shared filesystem* atau mekanisme komunikasi
  antar-proses lain.
- **Kontrak layanan** dipublikasikan lewat *routing* eksplisit
  (`routes/api.php`) berformat JSON:API standar (`/v1/{resource}`) —
  layanan lain (frontend) berinteraksi murni lewat HTTP+port, sesuai
  Faktor VII.

**Status:** Diterapkan.

---

## VIII. Concurrency

> Skalakan lewat model proses: tambahkan proses horizontal (*scale out*),
> bukan hanya memperbesar satu proses (*scale up*). Proses yang berbeda
> jenis (web, worker) diperlakukan sebagai tipe proses independen.

**Implementasi:**

- Karena backend sepenuhnya *stateless* (Faktor VI) dan basis data yang
  dipakai (SQLite untuk portabilitas dev, atau PostgreSQL/MySQL di
  production skala besar) adalah *backing service* eksternal terhadap
  proses PHP, backend ini dapat dijalankan sebagai **banyak proses PHP-FPM
  identik secara paralel di belakang *load balancer*** tanpa koordinasi
  antar-proses (*share-nothing*) — permintaan mana pun dapat dilayani oleh
  proses mana pun karena token, sesi, dan data seluruhnya berada di
  *backing service*, bukan memori lokal.
- **Tipe proses dipisah secara eksplisit:**
  - *Proses web* — melayani HTTP (`php artisan serve` / PHP-FPM) —
    diskalakan horizontal sesuai beban traffic API.
  - *Proses worker* — `QUEUE_CONNECTION=database` sudah disiapkan sebagai
    fondasi untuk *queue worker* terpisah (`php artisan queue:work`) bila
    pekerjaan berat (mis. notifikasi massal saat status Proposal berubah,
    ekspor laporan) dipindah ke antrian asinkron di masa depan — proses
    ini dapat diskalakan independen dari proses web.
  - *Proses admin/one-off* — lihat Faktor XII.
- Uji otomatis dijalankan di atas SQLite **in-memory** per proses test
  (`phpunit.xml`: `DB_DATABASE=:memory:`) — membuktikan tiap proses test
  independen dan dapat dijalankan paralel tanpa berbagi *state*.

**Status:** Diterapkan secara desain (arsitektur *share-nothing* siap
skala). Catatan produksi: untuk konkurensi tulis tinggi, SQLite sebaiknya
digantikan basis data server (PostgreSQL/MySQL) karena SQLite membatasi
satu penulis pada satu waktu — pertukaran ini hanya butuh perubahan
`.env` (Faktor IV), tidak mengubah kode.

---

## IX. Disposability

> Proses harus dapat dinyalakan/dimatikan cepat, untuk memungkinkan
> *scaling* elastis, *deploy* cepat, dan pemulihan cepat dari kegagalan.
> *Startup time* harus minim dan *shutdown* harus *graceful* (menyelesaikan
> permintaan yang sedang berjalan, mengembalikan *job* antrian yang belum
> selesai).

**Implementasi:**

- **Startup cepat & idempoten** — `php artisan serve` dan proses PHP-FPM
  Laravel siap menerima permintaan dalam hitungan detik tanpa langkah
  *warm-up* manual. Skrip regenerasi proyek
  (`generate.py && generate_jsonapi.py && generate_policies.py &&
  migrate:fresh --force`) bersifat idempoten — dapat dijalankan berulang
  kali dari kondisi kosong dan selalu menghasilkan skema+kode yang sama,
  memudahkan *restart* bersih kapan pun dibutuhkan.
- **Migrasi basis data yang aman di-*restart*** — setiap migrasi Laravel
  bernomor urut dan dilacak di tabel `migrations`; menjalankan
  `php artisan migrate` berulang tidak mengulang migrasi yang sudah
  diterapkan (*re-entrant*), sehingga proses *deploy* ulang aman.
- **Tidak ada *state* proses yang hilang saat *shutdown*** — karena
  seluruh data persisten berada di *backing service* (basis data), proses
  PHP dapat dihentikan kapan pun tanpa risiko kehilangan data transaksi
  yang sudah tersimpan (Faktor VI, IV). Permintaan yang sedang diproses
  saat *shutdown* akan diselesaikan oleh mekanisme *graceful shutdown*
  server aplikasi (PHP-FPM `pm.max_requests`/*graceful reload*, atau
  *Kubernetes* `terminationGracePeriodSeconds` bila dikontainerkan).
- **Uji cepat & terisolasi** — 173 test (Feature + Unit) berjalan dalam
  waktu ~2,75 detik menggunakan SQLite in-memory yang dibuat dan dibuang
  ulang per test (`RefreshDatabase`), mendemonstrasikan pola *start
  fresh → run → dispose* yang konsisten dengan prinsip *disposability*.

**Status:** Diterapkan.

---

## X. Dev/Prod Parity

> Perkecil sedapat mungkin kesenjangan antara lingkungan pengembangan dan
> production — dalam hal waktu (deploy cepat & sering), personel (yang
> menulis kode yang men-*deploy*-nya), dan **alat** (*backing service*
> yang identik jenisnya di semua lingkungan).

**Implementasi:**

- **Kesenjangan alat (*tools gap*) diminimalkan** — arsitektur
  konfigurasi-lewat-environment (Faktor III, IV) memastikan kode yang
  berjalan di dev dan production **identik**; hanya nilai `.env`
  (`DB_CONNECTION`, `CACHE_STORE`, dll.) yang berbeda. SQLite dipilih
  khusus untuk portabilitas pengembangan/demo, tetapi karena akses data
  seluruhnya lewat Eloquent (bukan raw SQL dialek-spesifik), *upgrade*
  ke basis data server di production tidak memerlukan perubahan kode
  aplikasi — hanya `.env` dan menjalankan `php artisan migrate` pada
  koneksi baru.
- **Kesenjangan waktu diminimalkan** — Skrip generator (`spec/generate*.py`)
  memungkinkan seluruh lapisan (migrasi, model, schema JSON:API, policy,
  test) diregenerasi dan diverifikasi ulang secara instan begitu
  `entities.json` berubah, sehingga perubahan skema dapat mengalir dari
  spesifikasi ke kode ke lingkungan *live* dalam waktu singkat, bukan
  menunggu siklus rilis manual berhari-hari.
- **Kesenjangan personel diminimalkan** — dokumentasi ini, komentar kode
  berbahasa Indonesia, dan test otomatis (173 test) memungkinkan siapa pun
  di tim (bukan hanya penulis kode asli) memahami dan men-*deploy* aturan
  bisnis dengan percaya diri.
- **Versi dependensi terkunci identik di semua lingkungan** lewat
  `composer.lock` (bukan hanya *version range* di `composer.json`) —
  memastikan `composer install` di dev dan production menginstal versi
  paket **yang benar-benar sama** (PHP 8.5.4, Laravel 12.68.0,
  laravel-json-api/laravel 5.3.0, Sanctum 4.3.3 pada saat dokumen ini
  ditulis).

**Status:** Diterapkan. Catatan: bila production memakai basis data
server (bukan SQLite), disarankan menjalankan *staging* dengan basis data
server yang sama (bukan SQLite) sebelum rilis akhir, untuk menguji
perilaku *locking*/konkurensi yang berbeda dari SQLite (lihat Faktor
VIII).

---

## XI. Logs

> Perlakukan log sebagai *event stream* — jangan kelola *routing*/
> penyimpanan log dari dalam aplikasi; alirkan log tanpa buffer ke
> `stdout`, biarkan lingkungan eksekusi (platform) yang menangani
> pengumpulan, agregasi, dan pengarsipannya.

**Implementasi:**

- Laravel menyediakan abstraksi *logging channel* (`config/logging.php`)
  yang memisahkan **apa yang dicatat** (kode aplikasi memanggil
  `Log::info()`/`Log::error()`, dll.) dari **ke mana log dialirkan**
  (ditentukan oleh `.env`, bukan kode):
  - Pengembangan: `LOG_CHANNEL=stack` → `LOG_STACK=single` (berkas
    `storage/logs/laravel.log`) — memudahkan *debugging* lokal.
  - Production (rekomendasi *deploy* kontainer): cukup ubah
    `LOG_CHANNEL=stderr` di `.env` — channel `stderr` sudah tersedia
    bawaan (`config/logging.php`, driver `monolog` dengan *handler*
    `StreamHandler` ke `php://stderr`) — **tanpa mengubah kode aplikasi
    sedikit pun** — sehingga log mengalir sebagai *stream* tak
    ter-*buffer* yang dapat dikumpulkan oleh platform (Docker/Kubernetes
    log driver, systemd-journald, dsb.), tepat sesuai prinsip Faktor XI.
  - Kanal lain yang sudah tersedia tanpa kode tambahan: `syslog`,
    `papertrail`, `slack` (notifikasi *log* kritis) — seluruhnya
    tinggal-pilih lewat `.env`.
- Aplikasi **tidak** menulis logika *rotate*/kompres/kirim-log sendiri
  (mis. tidak ada `cron` kustom untuk membersihkan log) — pengarsipan/
  rotasi diserahkan ke platform (atau driver `daily` bawaan Laravel bila
  dibutuhkan retensi lokal sederhana, diatur murni lewat `.env`
  `LOG_DAILY_DAYS`).
- Level log (`LOG_LEVEL=debug` untuk dev, disarankan `warning`/`error`
  untuk production demi mengurangi *noise*) juga dikendalikan lewat
  `.env`, bukan hard-coded.

**Status:** Diterapkan (infrastruktur logging Laravel sudah mendukung
model *event stream* penuh). Rekomendasi *deploy* production: set
`LOG_CHANNEL=stderr` (atau `stack` dengan `stderr` di dalamnya) agar log
tertangkap oleh *log collector* platform, alih-alih menulis ke berkas
lokal `storage/logs/laravel.log` yang tidak persisten di proses
*disposable* (Faktor IX).

---

## XII. Admin Processes

> Jalankan tugas administratif/manajemen satu-kali (migrasi basis data,
> konsol REPL, skrip pembersihan data) sebagai proses *satu-kali* (one-off)
> di lingkungan yang identik dengan proses *run* biasa — pakai *release*
> yang sama, konfigurasi yang sama, kode yang sama.

**Implementasi:**

- Seluruh tugas administratif proyek ini dijalankan lewat **Laravel
  Artisan CLI**, yang secara desain memuat *bootstrap* aplikasi penuh
  (config, service container, koneksi basis data) — identik dengan yang
  dipakai proses `run` normal — sehingga tidak ada "jalur pintas" konfig
  berbeda untuk tugas admin:
  - Migrasi skema: `php artisan migrate` / `migrate:fresh --force`
  - Konsol REPL interaktif: `php artisan tinker` (paket `laravel/tinker`
    sudah terpasang) — untuk inspeksi data ad-hoc tanpa menulis skrip
    sekali pakai di luar basis kode.
  - *Seeding* data awal: `php artisan db:seed` (memakai *seeder* per
    entitas yang sudah dibuat bersama model).
  - Uji otomatis: `php artisan test` (dan variannya
    `--testsuite=Unit`/`--testsuite=Feature`) — proses satu-kali yang
    memvalidasi *behaviour* memakai *codebase* dan *config test* yang
    sama persis dengan yang dipakai proses lain (`phpunit.xml`).
  - Regenerasi kode dari spesifikasi:
    `python3 spec/generate.py && python3 spec/generate_jsonapi.py &&
    python3 spec/generate_policies.py` — dijalankan sebagai proses
    *admin* satu-kali terpisah dari proses *run* (server web), tidak
    pernah dipanggil otomatis saat *runtime* melayani permintaan.
- Tidak ada skrip administratif yang memerlukan lingkungan/*dependency*
  berbeda dari aplikasi utama — semuanya berjalan di atas *runtime* PHP +
  `vendor/` yang sama, membaca `.env`/`config/*.php` yang sama, sehingga
  tidak ada risiko *config drift* antara proses admin dan proses
  produksi.

**Status:** Diterapkan.

---

## Catatan Implementasi Frontend Svelte (Uncoupled)

Frontend telah dibangun sebagai basis kode terpisah (`simppm-frontend/`,
Svelte 5 + Vite), yang:

- Mendeklarasikan dependensinya sendiri lewat `package.json` (Faktor II).
- Membaca alamat API backend dari variabel *environment* Vite
  (`VITE_API_BASE_URL`), bukan di-*hardcode* (Faktor III).
- Memperlakukan backend JSON:API murni sebagai *backing service* yang
  diakses lewat URL+port, dapat ditukar antar-lingkungan hanya lewat
  konfigurasi (Faktor IV, VII).
- Berjalan sebagai proses statis/CDN yang *stateless* di sisi *server*
  (state autentikasi disimpan di sisi klien sebagai token, bukan di
  server) — konsisten dengan Faktor VI, VIII.
- Diverifikasi lulus untuk seluruh alur CRUD (tambah, ubah, hapus) dan
  seluruh jenis input (teks, enum, tanggal, angka, desimal, checkbox
  boolean, dropdown relasi) terhadap backend JSON:API yang sama yang
  diuji oleh 173 test otomatis pada bagian ini.

**Status:** Diterapkan. Detail arsitektur, struktur folder, cara
menjalankan, dan akun demo — lihat `simppm-frontend/README.md`.

---

## Referensi

- Wiggins, A. *The Twelve-Factor App*. [12factor.net](https://12factor.net/)
- [Laravel 12.x Configuration](https://laravel.com/docs/12.x/configuration)
- [Laravel 12.x Logging](https://laravel.com/docs/12.x/logging)
- [Laravel Sanctum — SPA Authentication](https://laravel.com/docs/12.x/sanctum)
- [JSON:API Specification](https://jsonapi.org/)
