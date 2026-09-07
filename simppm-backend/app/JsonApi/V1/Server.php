<?php

namespace App\JsonApi\V1;

use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{

    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        // no-op
    }

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    protected function allSchemas(): array
    {
        return [
            \App\JsonApi\V1\Prodi\ProdiSchema::class,
            \App\JsonApi\V1\Dosen\DosenSchema::class,
            \App\JsonApi\V1\Mahasiswa\MahasiswaSchema::class,
            \App\JsonApi\V1\Renstra\RenstraSchema::class,
            \App\JsonApi\V1\PetaJalan\PetaJalanSchema::class,
            \App\JsonApi\V1\Pedoman\PedomanSchema::class,
            \App\JsonApi\V1\SaranaPrasarana\SaranaPrasaranaSchema::class,
            \App\JsonApi\V1\SkemaPendanaan\SkemaPendanaanSchema::class,
            \App\JsonApi\V1\Mitra\MitraSchema::class,
            \App\JsonApi\V1\Proposal\ProposalSchema::class,
            \App\JsonApi\V1\AnggotaTim\AnggotaTimSchema::class,
            \App\JsonApi\V1\Penilaian\PenilaianSchema::class,
            \App\JsonApi\V1\KlirensEtik\KlirensEtikSchema::class,
            \App\JsonApi\V1\Kontrak\KontrakSchema::class,
            \App\JsonApi\V1\PencairanDana\PencairanDanaSchema::class,
            \App\JsonApi\V1\Logbook\LogbookSchema::class,
            \App\JsonApi\V1\Monev\MonevSchema::class,
            \App\JsonApi\V1\IntegrasiKurikulum\IntegrasiKurikulumSchema::class,
            \App\JsonApi\V1\Publikasi\PublikasiSchema::class,
            \App\JsonApi\V1\Hki\HkiSchema::class,
            \App\JsonApi\V1\ProdukAdopsi\ProdukAdopsiSchema::class,
            \App\JsonApi\V1\LaporanAkhir\LaporanAkhirSchema::class,
            \App\JsonApi\V1\Spj\SpjSchema::class,
            \App\JsonApi\V1\Sitasi\SitasiSchema::class,
            \App\JsonApi\V1\Rekognisi\RekognisiSchema::class,
            \App\JsonApi\V1\Kerjasama\KerjasamaSchema::class,
            \App\JsonApi\V1\SurveiDampak\SurveiDampakSchema::class,
        ];
    }
}
