<?php

namespace App\JsonApi\V1\Mahasiswa;

// Aturan validasi body permintaan create/update untuk resource
// "mahasiswa". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class MahasiswaRequest extends ResourceRequest
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
            'nim' => ['required', 'string', 'max:15'],
            'nama' => ['required', 'string', 'max:100'],
        ];
    }
}
