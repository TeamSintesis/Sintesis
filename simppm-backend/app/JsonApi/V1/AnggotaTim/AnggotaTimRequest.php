<?php

namespace App\JsonApi\V1\AnggotaTim;

// Aturan validasi body permintaan create/update untuk resource
// "anggota-tim". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class AnggotaTimRequest extends ResourceRequest
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
            'dosen' => ['nullable'],
            'mahasiswa' => ['nullable'],
            'peran' => ['required', 'string', 'max:50'],
        ];
    }
}
