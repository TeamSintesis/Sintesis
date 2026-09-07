<?php

namespace App\JsonApi\V1\Sitasi;

// Aturan validasi body permintaan create/update untuk resource
// "sitasi". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SitasiRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'publikasi' => ['nullable'],
            'dosen' => ['required'],
            'jumlahSitasi' => ['required', 'integer'],
            'tahun' => ['required', 'integer', 'digits:4'],
            'sumber' => ['required', Rule::in(['google_scholar', 'sinta', 'scopus'])],
        ];
    }
}
