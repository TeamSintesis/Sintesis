<?php

namespace App\JsonApi\V1\Prodi;

// Aturan validasi body permintaan create/update untuk resource
// "prodi". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ProdiRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'namaProdi' => ['required', 'string', 'max:100'],
            'fakultas' => ['nullable', 'string', 'max:100'],
        ];
    }
}
