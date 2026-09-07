<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Prodi (Program Studi).
 *
 * Merepresentasikan entitas prodi pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Prodi extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'prodi';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_prodi',
        'fakultas'
    ];

    /**
     * Relasi ke daftar Dosen/Peneliti terkait (hasMany).
     */
    public function dosenList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dosen::class, 'prodi_id');
    }

    /**
     * Relasi ke daftar Mahasiswa terkait (hasMany).
     */
    public function mahasiswaList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'prodi_id');
    }

    /**
     * Relasi ke daftar Peta Jalan Penelitian/PkM terkait (hasMany).
     */
    public function petaJalanList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PetaJalan::class, 'prodi_id');
    }
}
