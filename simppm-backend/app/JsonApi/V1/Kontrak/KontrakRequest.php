<?php

namespace App\JsonApi\V1\Kontrak;

// Aturan validasi body permintaan create/update untuk resource
// "kontrak". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class KontrakRequest extends ResourceRequest
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
            'nomorSk' => ['required', 'string', 'max:50'],
            'tanggalKontrak' => ['required', 'date'],
            'nilaiKontrak' => ['required', 'numeric'],
        ];
    }
}
