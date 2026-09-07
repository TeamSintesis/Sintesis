<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Renstra (Rencana Strategis).
 *
 * Merepresentasikan entitas renstra pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Renstra extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'renstra';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis',
        'periode_mulai',
        'periode_akhir',
        'dokumen_url'
    ];

    /**
     * Relasi ke daftar Peta Jalan Penelitian/PkM terkait (hasMany).
     */
    public function petaJalanList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PetaJalan::class, 'renstra_id');
    }
}
