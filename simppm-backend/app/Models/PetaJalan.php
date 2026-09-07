<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model PetaJalan (Peta Jalan Penelitian/PkM).
 *
 * Merepresentasikan entitas peta_jalan pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class PetaJalan extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'peta_jalan';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'renstra_id',
        'prodi_id',
        'bidang_keilmuan',
        'tahun'
    ];

    /**
     * Relasi ke Rencana Strategis (belongsTo).
     */
    public function renstra(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Renstra::class, 'renstra_id');
    }

    /**
     * Relasi ke Program Studi (belongsTo).
     */
    public function prodi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    /**
     * Relasi ke daftar Proposal Penelitian/PkM terkait (hasMany).
     */
    public function proposalList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Proposal::class, 'peta_jalan_id');
    }
}
