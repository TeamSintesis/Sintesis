<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Aturan Bisnis SIMPPM
|--------------------------------------------------------------------------
|
| Berkas ini memisahkan nilai-nilai konfigurasi yang dapat berubah
| antar-lingkungan (development/staging/production) dari kode aplikasi,
| sesuai prinsip 12-factor app ketiga: "Config" -- simpan konfigurasi
| pada environment, bukan tertanam (hard-coded) di dalam kode sumber.
|
| Seluruh nilai di bawah ini dapat dioverride melalui variabel .env
| tanpa perlu mengubah kode.
|
*/

return [

    /*
    |----------------------------------------------------------------------
    | Ambang Batas Skor Penilaian
    |----------------------------------------------------------------------
    |
    | Rata-rata skor Penilaian (dari reviewer yang telah difinalisasi)
    | minimum agar sebuah Proposal dapat disetujui (status = 'disetujui').
    |
    */
    'ambang_batas_skor_penilaian' => (float) env('SIMPPM_AMBANG_BATAS_SKOR', 70.0),

];
