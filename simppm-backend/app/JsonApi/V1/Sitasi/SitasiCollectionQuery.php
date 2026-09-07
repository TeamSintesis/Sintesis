<?php

namespace App\JsonApi\V1\Sitasi;

// Aturan validasi parameter query untuk permintaan koleksi
// "sitasi" (GET /sitasi).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class SitasiCollectionQuery extends ResourceQuery
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
