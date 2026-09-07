<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Rekognisi (Rekognisi).
 *
 * Merepresentasikan entitas rekognisi pada fase dampak SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Rekognisi extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'rekognisi';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dosen_id',
        'jenis',
        'deskripsi',
        'tahun'
    ];

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
