<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Mahasiswa (Mahasiswa).
 *
 * Merepresentasikan entitas mahasiswa pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prodi_id',
        'nim',
        'nama'
    ];

    /**
     * Relasi ke Program Studi (belongsTo).
     */
    public function prodi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    /**
     * Relasi ke daftar Anggota Tim Proposal terkait (hasMany).
     */
    public function anggotaTimList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'mahasiswa_id');
    }
}
