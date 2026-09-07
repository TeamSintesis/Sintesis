<?php

namespace App\JsonApi\V1\Renstra;

// Aturan validasi body permintaan create/update untuk resource
// "renstra". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class RenstraRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(['penelitian', 'pkm'])],
            'periodeMulai' => ['required', 'integer', 'digits:4'],
            'periodeAkhir' => ['required', 'integer', 'digits:4'],
            'dokumenUrl' => ['nullable', 'string', 'max:255'],
        ];
    }
}
