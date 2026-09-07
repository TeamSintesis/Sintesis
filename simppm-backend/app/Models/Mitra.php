<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Mitra (Mitra Eksternal).
 *
 * Merepresentasikan entitas mitra pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Mitra extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'mitra';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_mitra',
        'jenis_mitra',
        'kontak',
        'alamat'
    ];

    /**
     * Relasi ke daftar Produk/Purwarupa Adopsi terkait (hasMany).
     */
    public function produkAdopsiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProdukAdopsi::class, 'mitra_id');
    }

    /**
     * Relasi ke daftar Kerja Sama terkait (hasMany).
     */
    public function kerjasamaList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Kerjasama::class, 'mitra_id');
    }

    /**
     * Relasi ke daftar Survei Dampak terkait (hasMany).
     */
    public function surveiDampakList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SurveiDampak::class, 'mitra_id');
    }
}
