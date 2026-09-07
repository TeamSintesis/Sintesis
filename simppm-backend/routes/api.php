<?php

// Registrasi rute JSON:API untuk seluruh 27 resource SIMPPM (server "v1").
// Dibuat otomatis oleh spec/generate_jsonapi.py, ditambah rute autentikasi
// manual (login/logout) di bagian bawah berkas.

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

JsonApiRoute::server('v1')->prefix('v1')->middleware('auth:sanctum')->resources(function (ResourceRegistrar $server) {
    $server->resource('prodi', JsonApiController::class);
    $server->resource('dosen', JsonApiController::class);
    $server->resource('mahasiswa', JsonApiController::class);
    $server->resource('renstra', JsonApiController::class);
    $server->resource('peta-jalan', JsonApiController::class);
    $server->resource('pedoman', JsonApiController::class);
    $server->resource('sarana-prasarana', JsonApiController::class);
    $server->resource('skema-pendanaan', JsonApiController::class);
    $server->resource('mitra', JsonApiController::class);
    $server->resource('proposal', JsonApiController::class);
    $server->resource('anggota-tim', JsonApiController::class);
    $server->resource('penilaian', JsonApiController::class);
    $server->resource('klirens-etik', JsonApiController::class);
    $server->resource('kontrak', JsonApiController::class);
    $server->resource('pencairan-dana', JsonApiController::class);
    $server->resource('logbook', JsonApiController::class);
    $server->resource('monev', JsonApiController::class);
    $server->resource('integrasi-kurikulum', JsonApiController::class);
    $server->resource('publikasi', JsonApiController::class);
    $server->resource('hki', JsonApiController::class);
    $server->resource('produk-adopsi', JsonApiController::class);
    $server->resource('laporan-akhir', JsonApiController::class);
    $server->resource('spj', JsonApiController::class);
    $server->resource('sitasi', JsonApiController::class);
    $server->resource('rekognisi', JsonApiController::class);
    $server->resource('kerjasama', JsonApiController::class);
    $server->resource('survei-dampak', JsonApiController::class);
});

// Rute autentikasi berbasis token Sanctum (stateless, untuk SPA Svelte
// yang uncoupled / terpisah dari backend).
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});
