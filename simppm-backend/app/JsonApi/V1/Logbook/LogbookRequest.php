<?php

namespace App\JsonApi\V1\Logbook;

// Aturan validasi body permintaan create/update untuk resource
// "logbook". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class LogbookRequest extends ResourceRequest
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
            'periode' => ['required', 'string', 'max:20'],
            'isiKemajuan' => ['required', 'string', 'max:255'],
            'tanggalIsi' => ['required', 'date'],
        ];
    }
}
