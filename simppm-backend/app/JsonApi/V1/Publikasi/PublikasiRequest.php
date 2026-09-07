<?php

namespace App\JsonApi\V1\Publikasi;

// Aturan validasi body permintaan create/update untuk resource
// "publikasi". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PublikasiRequest extends ResourceRequest
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
            'judul' => ['required', 'string', 'max:250'],
            'jurnalProsiding' => ['nullable', 'string', 'max:150'],
            'indeksasi' => ['required', Rule::in(['scopus', 'sinta_1', 'sinta_2', 'sinta_3', 'sinta_4', 'sinta_5', 'sinta_6', 'nasional_non_sinta'])],
            'tahun' => ['required', 'integer', 'digits:4'],
            'doi' => ['nullable', 'string', 'max:100'],
            'lisensi' => ['nullable', 'string', 'max:20'],
        ];
    }
}
