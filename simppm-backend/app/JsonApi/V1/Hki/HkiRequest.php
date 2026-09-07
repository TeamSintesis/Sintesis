<?php

namespace App\JsonApi\V1\Hki;

// Aturan validasi body permintaan create/update untuk resource
// "hki". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class HkiRequest extends ResourceRequest
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
            'jenis' => ['required', Rule::in(['paten', 'hak_cipta', 'desain_industri', 'merek', 'lainnya'])],
            'status' => ['required', Rule::in(['diajukan', 'diperiksa_substantif', 'terbit', 'ditolak'])],
            'nomorSertifikat' => ['nullable', 'string', 'max:50'],
        ];
    }
}
