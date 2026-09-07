<?php

namespace App\JsonApi\V1\Sitasi;

// Skema JSON:API untuk entitas Sitasi (Sitasi).
// Berkas ini dibuat otomatis oleh spec/generate_jsonapi.py — jangan diubah
// manual, ubah spec/entities.json lalu jalankan ulang generator.

use App\Models\Sitasi;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

/**
 * Skema JSON:API untuk resource "sitasi".
 *
 * Memetakan atribut database (snake_case) ke field JSON:API (camelCase)
 * serta mendefinisikan relasi dan filter yang tersedia bagi klien.
 */
class SitasiSchema extends Schema
{
    /**
     * Model Eloquent yang bersesuaian dengan skema ini.
     *
     * @var string
     */
    public static string $model = Sitasi::class;

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
        return 'sitasi';
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
            BelongsTo::make('publikasi')->type('publikasi'),
            BelongsTo::make('dosen')->type('dosen'),
            Number::make('jumlahSitasi', 'jumlah_sitasi')->sortable(),
            Str::make('tahun', 'tahun')->sortable(),
            Str::make('sumber', 'sumber'),
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
            Where::make('publikasiId', 'publikasi_id'),
            Where::make('dosenId', 'dosen_id'),
            Where::make('tahun', 'tahun'),
            Where::make('sumber', 'sumber'),
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
