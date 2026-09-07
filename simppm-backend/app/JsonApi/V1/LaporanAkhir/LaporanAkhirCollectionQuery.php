<?php

namespace App\JsonApi\V1\LaporanAkhir;

// Aturan validasi parameter query untuk permintaan koleksi
// "laporan-akhir" (GET /laporan-akhir).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class LaporanAkhirCollectionQuery extends ResourceQuery
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
