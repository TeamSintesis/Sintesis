<?php

namespace App\JsonApi\V1\Proposal;

// Aturan validasi parameter query untuk permintaan satu resource
// "proposal" (GET /proposal/{id}).
// Dibuat otomatis oleh spec/generate_jsonapi.py.

use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class ProposalQuery extends ResourceQuery
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
