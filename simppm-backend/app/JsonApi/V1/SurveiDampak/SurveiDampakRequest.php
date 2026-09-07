<?php

namespace App\JsonApi\V1\SurveiDampak;

// Aturan validasi body permintaan create/update untuk resource
// "survei-dampak". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SurveiDampakRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'mitra' => ['required'],
            'proposal' => ['nullable'],
            'hasil' => ['nullable', 'string', 'max:255'],
            'testimoni' => ['nullable', 'string', 'max:255'],
        ];
    }
}
