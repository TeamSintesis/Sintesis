<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SkemaPendanaan (Skema Pendanaan).
 *
 * Merepresentasikan entitas skema_pendanaan pada fase masukan SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class SkemaPendanaan extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'skema_pendanaan';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_skema',
        'jenis',
        'plafon_dana',
        'sumber_dana'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'plafon_dana' => 'decimal:2'
    ];

    /**
     * Relasi ke daftar Proposal Penelitian/PkM terkait (hasMany).
     */
    public function proposalList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Proposal::class, 'skema_pendanaan_id');
    }
}
