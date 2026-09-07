<?php

namespace App\JsonApi\V1\Dosen;

// Skema JSON:API untuk entitas Dosen (Dosen/Peneliti).
// Berkas ini dibuat otomatis oleh spec/generate_jsonapi.py — jangan diubah
// manual, ubah spec/entities.json lalu jalankan ulang generator.

use App\Models\Dosen;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

/**
 * Skema JSON:API untuk resource "dosen".
 *
 * Memetakan atribut database (snake_case) ke field JSON:API (camelCase)
 * serta mendefinisikan relasi dan filter yang tersedia bagi klien.
 */
class DosenSchema extends Schema
{
    /**
     * Model Eloquent yang bersesuaian dengan skema ini.
     *
     * @var string
     */
    public static string $model = Dosen::class;

    /**
     * Tentukan jenis resource JSON:API secara eksplisit (bukan hasil tebakan
     * otomatis dari nama kelas), agar sesuai dengan resource_type pada
     * spec/entities.json — termasuk untuk kata Indonesia yang tidak boleh
     * dipluralkan mengikuti aturan bahasa Inggris (misalnya "dosen", bukan
     * "dosens").
     *
     * @return string
     */
    public static function type(): string
    {
        return 'dosen';
    }

    /**
     * Daftar field (atribut & relasi) yang diekspos melalui API.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            BelongsTo::make('prodi')->type('prodi'),
            Str::make('nidn', 'nidn'),
            Str::make('nama', 'nama'),
            Str::make('jabatanFungsional', 'jabatan_fungsional'),
            Str::make('bidangKepakaran', 'bidang_kepakaran'),
            Str::make('idSinta', 'id_sinta'),
            Str::make('scopusId', 'scopus_id'),
            Str::make('orcid', 'orcid'),
            HasMany::make('proposalList')->type('proposal')->readOnly(),
            HasMany::make('anggotaTimList')->type('anggota-tim')->readOnly(),
            HasMany::make('penilaianList')->type('penilaian')->readOnly(),
            HasMany::make('monevList')->type('monev')->readOnly(),
            HasMany::make('sitasiList')->type('sitasi')->readOnly(),
            HasMany::make('rekognisiList')->type('rekognisi')->readOnly(),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
        ];
    }

    /**
     * Daftar filter query yang dapat digunakan klien (?filter[...]=...).
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            Where::make('prodiId', 'prodi_id'),
        ];
    }

    /**
     * Konfigurasi paginasi berbasis halaman (page[number], page[size]).
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
