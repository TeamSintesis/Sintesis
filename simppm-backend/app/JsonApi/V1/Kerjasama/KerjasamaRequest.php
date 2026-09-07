<?php

namespace App\JsonApi\V1\Kerjasama;

// Aturan validasi body permintaan create/update untuk resource
// "kerjasama". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class KerjasamaRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'mitra' => ['required'],
            'ruangLingkup' => ['required', 'string', 'max:200'],
            'tanggalMulai' => ['required', 'date'],
            'tanggalAkhir' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['aktif', 'berakhir', 'diperpanjang'])],
        ];
    }
}
