<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Kerjasama (Kerja Sama).
 *
 * Merepresentasikan entitas kerjasama pada fase dampak SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Kerjasama extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'kerjasama';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mitra_id',
        'ruang_lingkup',
        'tanggal_mulai',
        'tanggal_akhir',
        'status'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'tanggal_mulai' => 'date',
            'tanggal_akhir' => 'date'
    ];

    /**
     * Relasi ke Mitra Eksternal (belongsTo).
     */
    public function mitra(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Kembalikan ID mitra eksternal pemilik data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikMitraId(): ?int
    {
        return $this->mitra_id;
    }
}
