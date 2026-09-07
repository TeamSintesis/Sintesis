# SIMPPM Frontend (Svelte, Uncoupled)

Frontend Sistem Informasi Pengelolaan Penelitian dan Pengabdian kepada
Masyarakat (SIMPPM) — dibangun dengan Svelte 5 + Vite, sebagai **basis
kode yang sepenuhnya terpisah (uncoupled)** dari backend Laravel/JSON:API.
Frontend ini hanya berkomunikasi dengan backend lewat HTTP (JSON:API),
tidak berbagi kode, proses, maupun basis data apa pun dengan backend.

Selaras dengan prinsip *12-factor app* yang diterapkan di backend (lihat
`simppm-backend/docs/12-FACTOR-APP.md`):

- **Codebase terpisah** — repositori/folder sendiri, dapat dikembangkan,
  diuji, dan di-*deploy* independen dari backend.
- **Config lewat environment** — alamat API backend dibaca dari variabel
  lingkungan Vite (`VITE_API_BASE_URL`), tidak pernah di-*hardcode*.
- **Backend sebagai backing service** — backend JSON:API diperlakukan
  sebagai resource yang diakses lewat URL, dapat ditukar antar-lingkungan
  hanya lewat konfigurasi, tanpa mengubah kode frontend.
- **Stateless di sisi build** — hasil `npm run build` adalah aset statis
  (HTML/JS/CSS) yang bisa disajikan oleh server web statis apa pun (Nginx,
  Netlify, S3+CDN, dll); status login disimpan di sisi klien (browser
  storage), bukan di server.

## Arsitektur & Struktur Folder

```
src/
├─ app.css                  # Token desain global & kelas utilitas (.btn, .card, .field, dst.)
├─ main.js                  # Entry point Vite/Svelte
├─ App.svelte               # Root shell: routing (svelte-spa-router) + penjaga rute (route guard)
├─ lib/
│  ├─ api.js                # Klien HTTP JSON:API tipis (fetch + header Accept/Authorization)
│  ├─ auth.js                # Login/logout, penyimpanan token & sesi, daftar label peran
│  ├─ entities.meta.js       # Metadata 27 entitas (atribut, tipe, relasi) -- AUTO-GENERATED
│  └─ format.js              # Helper format tampilan (rupiah, tanggal, label relasi, dst.)
├─ components/
│  ├─ Sidebar.svelte         # Navigasi dikelompokkan per fase siklus PPM
│  ├─ Topbar.svelte          # Info pengguna aktif & tombol keluar
│  ├─ ErrorBanner.svelte     # Tampilan pesan error API (termasuk error validasi JSON:API)
│  ├─ Spinner.svelte         # Indikator memuat
│  └─ Pagination.svelte      # Kontrol halaman sebelumnya/berikutnya
└─ routes/
   ├─ Login.svelte           # Halaman masuk
   ├─ Dashboard.svelte       # Ringkasan jumlah data per entitas, dikelompokkan per fase
   ├─ ResourceList.svelte    # Tabel + paginasi GENERIK untuk entitas apa pun
   ├─ ResourceForm.svelte    # Form tambah/ubah GENERIK untuk entitas apa pun
   └─ NotFound.svelte
```

Halaman daftar dan form **tidak ditulis satu per satu untuk 27 entitas**.
Keduanya adalah komponen generik yang membaca `entities.meta.js` (metadata
atribut, tipe kolom, dan relasi tiap entitas) untuk merender kolom tabel,
jenis input, dan dropdown relasi secara otomatis. Ini memastikan konsistensi
penamaan dan perilaku di seluruh 27 entitas sekaligus mengurangi duplikasi.

Jika struktur entitas backend berubah (`spec/entities.json` di backend),
jalankan ulang generator berikut di folder backend agar `entities.meta.js`
tetap sinkron:

```bash
cd ../simppm-backend
python3 spec/generate_frontend_meta.py
```

## Menjalankan Secara Lokal

Prasyarat: Node.js 18+ dan backend SIMPPM (`simppm-backend`) sudah berjalan
(lihat README backend), karena frontend murni klien JSON:API.

```bash
npm install
cp .env.example .env      # sesuaikan VITE_API_BASE_URL bila backend tidak di 127.0.0.1:8123
npm run dev                # server pengembangan di http://localhost:5173
```

Build produksi (menghasilkan aset statis di `dist/`):

```bash
npm run build
npm run preview             # opsional: pratinjau build produksi secara lokal
```

## Konfigurasi (`.env`)

| Variabel | Keterangan | Contoh |
|---|---|---|
| `VITE_API_BASE_URL` | Alamat dasar backend JSON:API SIMPPM | `http://127.0.0.1:8123/api/v1` |

Variabel ini dibaca sekali saat build/`dev` oleh Vite (hanya variabel
berawalan `VITE_` yang dimuat ke bundel client-side). Untuk lingkungan
produksi, ganti nilainya lewat mekanisme environment platform *hosting*
(bukan dengan mengubah kode).

## Akun Demo

Seluruh akun memakai kata sandi **`password`** (hanya untuk lingkungan
pengembangan/demo -- lihat `simppm-backend/database/seeders/DatabaseSeeder.php`):

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

## Autentikasi & Otorisasi

- Login mengirim `POST {VITE_API_BASE_URL}/auth/login` (email+password),
  menerima token Sanctum, disimpan di `localStorage` lewat store `session`
  (`lib/auth.js`). Semua permintaan berikutnya mengirim header
  `Authorization: Bearer <token>`.
- Rute selain `/login` dilindungi route guard (`wajibLogin` di `App.svelte`
  via `svelte-spa-router/wrap`) -- pengguna belum login otomatis diarahkan
  ke `/login`.
- Otorisasi per aksi (siapa boleh membuat/mengubah/menghapus entitas apa)
  ditegakkan oleh **backend** (27 Policy class, lihat dokumentasi backend);
  frontend hanya menampilkan pesan error yang dikembalikan backend saat
  akses ditolak (HTTP 403), tidak menduplikasi logika otorisasi di klien.

## Konvensi Kode

- Seluruh komentar kode & label UI dalam Bahasa Indonesia, konsisten dengan
  penamaan model di backend (mis. `namaProdi`, `berisikoEtik`, `tahunUsulan`).
- Penamaan variabel/fungsi lokal juga memakai Bahasa Indonesia (`memuat`,
  `menyimpan`, `tanganiSubmit`, dst.) agar keseluruhan basis kode konsisten.
- Tipe input form (teks/angka/tanggal/enum/checkbox/dropdown relasi)
  diturunkan otomatis dari `atribut.type` pada `entities.meta.js`. Catatan
  penting: kolom bertipe `decimal` dan `year` dikirim ke backend sebagai
  **string** (bukan number JSON) karena skema JSON:API backend
  mendefinisikannya sebagai `Str::make` (demi presisi desimal yang aman);
  hanya kolom `integer` yang dikonversi ke number. Lihat komentar di
  `tanganiSubmit()` pada `ResourceForm.svelte`.

## Pengujian Manual yang Telah Dilakukan

Karena frontend ini murni klien statis tanpa logika bisnis (seluruh
validasi & aturan bisnis berada di backend, sudah tercakup 173 test
otomatis backend), verifikasi frontend dilakukan secara fungsional
end-to-end lewat browser terhadap backend yang berjalan:

- Login/logout seluruh alur, penjaga rute.
- CREATE (Program Studi), EDIT (Dosen/Peneliti termasuk field teks
  panjang), DELETE dengan konfirmasi (Program Studi).
- CREATE dengan seluruh variasi tipe field (enum, tahun, desimal,
  checkbox boolean, dropdown relasi wajib & opsional) pada entitas
  Proposal Penelitian/PkM.
- Tabel daftar menampilkan label relasi (bukan hanya ID) lewat
  `?include=` JSON:API.
- Console browser bersih dari error di seluruh alur di atas.

## Build & Deploy Terpisah dari Backend

`npm run build` menghasilkan folder `dist/` berisi aset statis murni
(tidak butuh Node.js/PHP untuk disajikan). Folder ini dapat di-*deploy*
ke *static hosting* apa pun (Nginx, S3+CloudFront, Netlify, Vercel, dst.)
secara independen dari lokasi/siklus rilis backend -- satu-satunya kaitan
adalah nilai `VITE_API_BASE_URL` yang harus menunjuk ke alamat backend
yang benar untuk lingkungan tersebut.
