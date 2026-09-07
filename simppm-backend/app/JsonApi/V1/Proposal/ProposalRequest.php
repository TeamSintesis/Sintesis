<?php

namespace App\JsonApi\V1\Proposal;

// Aturan validasi body permintaan create/update untuk resource
// "proposal". Dibuat otomatis oleh spec/generate_jsonapi.py.

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ProposalRequest extends ResourceRequest
{
    /**
     * Aturan validasi untuk atribut & relasi resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'pengusul' => ['required'],
            'skemaPendanaan' => ['required'],
            'petaJalan' => ['nullable'],
            'judul' => ['required', 'string', 'max:250'],
            'jenis' => ['required', Rule::in(['penelitian', 'pkm'])],
            'tahunUsulan' => ['required', 'integer', 'digits:4'],
            'status' => ['required', Rule::in(['draft', 'diajukan', 'direview', 'disetujui', 'ditolak'])],
            'rabTotal' => ['required', 'numeric'],
            'berisikoEtik' => ['required', 'boolean'],
        ];
    }
}
