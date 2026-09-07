<?php

namespace App\JsonApi\V1\Penilaian;

// Aturan validasi body permintaan create/update untuk resource
// "penilaian". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PenilaianRequest extends ResourceRequest
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
            'skor' => ['nullable', 'numeric'],
            'komentar' => ['nullable', 'string', 'max:255'],
            'statusFinalisasi' => ['required', 'boolean'],
            'tanggal' => ['nullable', 'date'],
        ];
    }
}
