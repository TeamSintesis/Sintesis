<?php

namespace App\JsonApi\V1\Kerjasama;

// Aturan validasi parameter query untuk permintaan satu resource
// "kerjasama" (GET /kerjasama/{id}).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class KerjasamaQuery extends ResourceQuery
{
    /**
     * Aturan validasi untuk parameter query permintaan.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fields' => ['nullable', 'array', JsonApiRule::fieldSets()],
            'include' => ['nullable', 'string', JsonApiRule::includePaths()],
            'withCount' => ['nullable', 'string', JsonApiRule::countable()],
        ];
    }
}
