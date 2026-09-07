<?php

namespace App\JsonApi\V1\LaporanAkhir;

// Aturan validasi body permintaan create/update untuk resource
// "laporan-akhir". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class LaporanAkhirRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'proposal' => ['required'],
            'dokumenUrl' => ['required', 'string', 'max:255'],
            'tanggalSubmit' => ['required', 'date'],
            'statusVerifikasi' => ['required', Rule::in(['belum', 'terverifikasi'])],
        ];
    }
}
