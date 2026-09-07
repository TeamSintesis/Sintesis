<?php

namespace App\JsonApi\V1\PencairanDana;

// Aturan validasi body permintaan create/update untuk resource
// "pencairan-dana". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class PencairanDanaRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'kontrak' => ['required'],
            'termin' => ['required', 'integer'],
            'jumlah' => ['required', 'numeric'],
            'tanggal' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['dijadwalkan', 'dicairkan', 'tertunda'])],
        ];
    }
}
