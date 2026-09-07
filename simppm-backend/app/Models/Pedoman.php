<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pedoman (Pedoman dan Kode Etik).
 *
 * Merepresentasikan entitas pedoman pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Pedoman extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'pedoman';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis',
        'versi',
        'tanggal_berlaku',
        'dokumen_url'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'tanggal_berlaku' => 'date'
    ];

}
