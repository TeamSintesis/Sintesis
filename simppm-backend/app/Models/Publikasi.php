<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Publikasi (Publikasi).
 *
 * Merepresentasikan entitas publikasi pada fase luaran SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Publikasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'publikasi';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'judul',
        'jurnal_prosiding',
        'indeksasi',
        'tahun',
        'doi',
        'lisensi'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Sitasi terkait (hasMany).
     */
    public function sitasiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sitasi::class, 'publikasi_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi proposal), untuk keperluan otorisasi berbasis kepemilikan
     * pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->proposal?->dosen_id;
    }
}
