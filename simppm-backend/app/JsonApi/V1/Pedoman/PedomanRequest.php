<?php

namespace App\JsonApi\V1\Pedoman;

// Aturan validasi body permintaan create/update untuk resource
// "pedoman". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PedomanRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'jenis' => ['required', 'string', 'max:50'],
            'versi' => ['required', 'string', 'max:20'],
            'tanggalBerlaku' => ['required', 'date'],
            'dokumenUrl' => ['nullable', 'string', 'max:255'],
        ];
    }
}
