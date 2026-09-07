<?php

namespace App\JsonApi\V1\PetaJalan;

// Aturan validasi body permintaan create/update untuk resource
// "peta-jalan". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PetaJalanRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'renstra' => ['required'],
            'prodi' => ['required'],
            'bidangKeilmuan' => ['required', 'string', 'max:150'],
            'tahun' => ['required', 'integer', 'digits:4'],
        ];
    }
}
