<?php

namespace App\JsonApi\V1\Spj;

// Aturan validasi body permintaan create/update untuk resource
// "spj". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SpjRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'laporanAkhir' => ['required'],
            'jumlahRealisasi' => ['required', 'numeric'],
            'dokumenUrl' => ['nullable', 'string', 'max:255'],
        ];
    }
}
