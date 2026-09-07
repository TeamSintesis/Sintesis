<?php

namespace App\JsonApi\V1\SkemaPendanaan;

// Aturan validasi body permintaan create/update untuk resource
// "skema-pendanaan". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SkemaPendanaanRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'namaSkema' => ['required', 'string', 'max:100'],
            'jenis' => ['required', Rule::in(['internal', 'eksternal'])],
            'plafonDana' => ['required', 'numeric'],
            'sumberDana' => ['nullable', 'string', 'max:100'],
        ];
    }
}
