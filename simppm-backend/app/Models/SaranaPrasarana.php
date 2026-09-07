<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SaranaPrasarana (Sarana dan Prasarana).
 *
 * Merepresentasikan entitas sarana_prasarana pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class SaranaPrasarana extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'sarana_prasarana';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_sarpras',
        'lokasi',
        'status'
    ];

}
