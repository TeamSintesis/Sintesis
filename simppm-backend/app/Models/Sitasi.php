<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Sitasi (Sitasi).
 *
 * Merepresentasikan entitas sitasi pada fase dampak SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Sitasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'sitasi';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'publikasi_id',
        'dosen_id',
        'jumlah_sitasi',
        'tahun',
        'sumber'
    ];

    /**
     * Relasi ke Publikasi (belongsTo).
     */
    public function publikasi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Publikasi::class, 'publikasi_id');
    }

    /**
     * Relasi ke Dosen/Peneliti (belongsTo).
     */
    public function dosen(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->dosen_id;
    }
}
