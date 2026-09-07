<?php

namespace App\JsonApi\V1\Dosen;

// Aturan validasi body permintaan create/update untuk resource
// "dosen". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class DosenRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'prodi' => ['required'],
            'nidn' => ['required', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:100'],
            'jabatanFungsional' => ['nullable', 'string', 'max:50'],
            'bidangKepakaran' => ['nullable', 'string', 'max:150'],
            'idSinta' => ['nullable', 'string', 'max:20'],
            'scopusId' => ['nullable', 'string', 'max:20'],
            'orcid' => ['nullable', 'string', 'max:25'],
        ];
    }
}
