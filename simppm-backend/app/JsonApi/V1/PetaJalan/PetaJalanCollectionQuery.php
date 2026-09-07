<?php

namespace App\JsonApi\V1\PetaJalan;

// Aturan validasi parameter query untuk permintaan koleksi
// "peta-jalan" (GET /peta-jalan).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class PetaJalanCollectionQuery extends ResourceQuery
{
    /**
     * Aturan validasi untuk parameter query permintaan koleksi.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fields' => ['nullable', 'array', JsonApiRule::fieldSets()],
            'filter' => ['nullable', 'array', JsonApiRule::filter()],
            'include' => ['nullable', 'string', JsonApiRule::includePaths()],
            'page' => ['nullable', 'array', JsonApiRule::page()],
            'sort' => ['nullable', 'string', JsonApiRule::sort()],
            'withCount' => ['nullable', 'string', JsonApiRule::countable()],
        ];
    }
}
