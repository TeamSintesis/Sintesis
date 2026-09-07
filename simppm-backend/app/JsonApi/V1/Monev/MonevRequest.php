<?php

namespace App\JsonApi\V1\Monev;

// Aturan validasi body permintaan create/update untuk resource
// "monev". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class MonevRequest extends ResourceRequest
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
            'reviewer' => ['required'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['terjadwal', 'selesai'])],
        ];
    }
}
