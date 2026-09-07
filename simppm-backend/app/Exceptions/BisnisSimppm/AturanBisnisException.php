<?php

namespace App\Exceptions\BisnisSimppm;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Kelas dasar (base class) untuk seluruh pengecualian (exception) aturan
 * bisnis SIMPPM.
 *
 * Setiap exception yang menurunkan kelas ini otomatis dirender sebagai
 * dokumen error JSON:API yang valid (bukan halaman error HTML Laravel
 * default), sehingga konsisten dengan format response backend JSON:API
 * lainnya.
 */
abstract class AturanBisnisException extends Exception
{
    /**
     * Kode status HTTP yang dikembalikan. 422 (Unprocessable Entity)
     * dipakai sebagai bawaan karena pelanggaran aturan bisnis pada
     * dasarnya adalah kegagalan validasi semantik data, bukan kesalahan
     * sintaksis permintaan.
     */
    protected int $statusHttp = 422;

    /**
     * Kode error singkat (machine-readable) untuk dikonsumsi oleh frontend,
     * agar frontend dapat menampilkan pesan yang sesuai tanpa mem-parsing
     * teks bebas.
     */
    abstract public function kodeError(): string;

    /**
     * Render exception ini menjadi dokumen error JSON:API.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'jsonapi' => ['version' => '1.0'],
            'errors' => [[
                'status' => (string) $this->statusHttp,
                'code' => $this->kodeError(),
                'title' => class_basename($this),
                'detail' => $this->getMessage(),
            ]],
        ], $this->statusHttp);
    }
}
