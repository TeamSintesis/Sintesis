<?php

namespace App\JsonApi\V1\IntegrasiKurikulum;

// Aturan validasi body permintaan create/update untuk resource
// "integrasi-kurikulum". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class IntegrasiKurikulumRequest extends ResourceRequest
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
            'mataKuliah' => ['required', 'string', 'max:100'],
            'rpsTautan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
