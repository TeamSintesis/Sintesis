<?php

namespace App\JsonApi\V1\Proposal;

// Skema JSON:API untuk entitas Proposal (Proposal Penelitian/PkM).
// Berkas ini dibuat otomatis oleh spec/generate_jsonapi.py — jangan diubah
// manual, ubah spec/entities.json lalu jalankan ulang generator.

use App\Models\Proposal;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

/**
 * Skema JSON:API untuk resource "proposal".
 *
 * Memetakan atribut database (snake_case) ke field JSON:API (camelCase)
 * serta mendefinisikan relasi dan filter yang tersedia bagi klien.
 */
class ProposalSchema extends Schema
{
    /**
     * Model Eloquent yang bersesuaian dengan skema ini.
     *
     * @var string
     */
    public static string $model = Proposal::class;

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
        return 'proposal';
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
            BelongsTo::make('pengusul')->type('dosen'),
            BelongsTo::make('skemaPendanaan')->type('skema-pendanaan'),
            BelongsTo::make('petaJalan')->type('peta-jalan'),
            Str::make('judul', 'judul'),
            Str::make('jenis', 'jenis'),
            Str::make('tahunUsulan', 'tahun_usulan')->sortable(),
            Str::make('status', 'status'),
            Str::make('rabTotal', 'rab_total'),
            Boolean::make('berisikoEtik', 'berisiko_etik'),
            HasMany::make('anggotaTimList')->type('anggota-tim')->readOnly(),
            HasMany::make('penilaianList')->type('penilaian')->readOnly(),
            HasMany::make('klirensEtikList')->type('klirens-etik')->readOnly(),
            HasMany::make('kontrakList')->type('kontrak')->readOnly(),
            HasMany::make('logbookList')->type('logbook')->readOnly(),
            HasMany::make('monevList')->type('monev')->readOnly(),
            HasMany::make('integrasiKurikulumList')->type('integrasi-kurikulum')->readOnly(),
            HasMany::make('publikasiList')->type('publikasi')->readOnly(),
            HasMany::make('hkiList')->type('hki')->readOnly(),
            HasMany::make('produkAdopsiList')->type('produk-adopsi')->readOnly(),
            HasMany::make('laporanAkhirList')->type('laporan-akhir')->readOnly(),
            HasMany::make('surveiDampakList')->type('survei-dampak')->readOnly(),
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
            Where::make('pengusulId', 'dosen_id'),
            Where::make('skemaPendanaanId', 'skema_pendanaan_id'),
            Where::make('petaJalanId', 'peta_jalan_id'),
            Where::make('jenis', 'jenis'),
            Where::make('tahunUsulan', 'tahun_usulan'),
            Where::make('status', 'status'),
            Where::make('berisikoEtik', 'berisiko_etik'),
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
