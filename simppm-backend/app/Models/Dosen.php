<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Dosen (Dosen/Peneliti).
 *
 * Merepresentasikan entitas dosen pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Dosen extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'dosen';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prodi_id',
        'nidn',
        'nama',
        'jabatan_fungsional',
        'bidang_kepakaran',
        'id_sinta',
        'scopus_id',
        'orcid'
    ];

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
        return $this->hasMany(Proposal::class, 'dosen_id');
    }

    /**
     * Relasi ke daftar Anggota Tim Proposal terkait (hasMany).
     */
    public function anggotaTimList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'dosen_id');
    }

    /**
     * Relasi ke daftar Penilaian Proposal terkait (hasMany).
     */
    public function penilaianList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penilaian::class, 'dosen_id');
    }

    /**
     * Relasi ke daftar Monitoring dan Evaluasi terkait (hasMany).
     */
    public function monevList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Monev::class, 'dosen_id');
    }

    /**
     * Relasi ke daftar Sitasi terkait (hasMany).
     */
    public function sitasiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sitasi::class, 'dosen_id');
    }

    /**
     * Relasi ke daftar Rekognisi terkait (hasMany).
     */
    public function rekognisiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rekognisi::class, 'dosen_id');
    }
}
