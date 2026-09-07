<?php

namespace App\JsonApi\V1\KlirensEtik;

// Aturan validasi body permintaan create/update untuk resource
// "klirens-etik". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class KlirensEtikRequest extends ResourceRequest
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
            'status' => ['required', Rule::in(['diajukan', 'ditinjau', 'disetujui', 'ditolak'])],
            'nomorSertifikat' => ['nullable', 'string', 'max:50'],
            'tanggalTerbit' => ['nullable', 'date'],
        ];
    }
}
