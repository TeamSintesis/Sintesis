<?php

namespace App\JsonApi\V1\Rekognisi;

// Aturan validasi body permintaan create/update untuk resource
// "rekognisi". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class RekognisiRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'dosen' => ['required'],
            'jenis' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'tahun' => ['required', 'integer', 'digits:4'],
        ];
    }
}
