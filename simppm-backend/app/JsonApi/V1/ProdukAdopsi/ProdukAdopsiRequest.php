<?php

namespace App\JsonApi\V1\ProdukAdopsi;

// Aturan validasi body permintaan create/update untuk resource
// "produk-adopsi". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ProdukAdopsiRequest extends ResourceRequest
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
            'mitra' => ['nullable'],
            'namaProduk' => ['required', 'string', 'max:150'],
            'buktiAdopsi' => ['nullable', 'string', 'max:255'],
        ];
    }
}
