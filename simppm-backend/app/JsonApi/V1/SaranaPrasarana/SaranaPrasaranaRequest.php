<?php

namespace App\JsonApi\V1\SaranaPrasarana;

// Aturan validasi body permintaan create/update untuk resource
// "sarana-prasarana". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SaranaPrasaranaRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'namaSarpras' => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['tersedia', 'digunakan', 'rusak'])],
        ];
    }
}
