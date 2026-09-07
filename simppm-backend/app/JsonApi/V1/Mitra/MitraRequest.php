<?php

namespace App\JsonApi\V1\Mitra;

// Aturan validasi body permintaan create/update untuk resource
// "mitra". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class MitraRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'namaMitra' => ['required', 'string', 'max:150'],
            'jenisMitra' => ['required', Rule::in(['industri', 'pemda', 'masyarakat', 'pt_lain'])],
            'kontak' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:200'],
        ];
    }
}
