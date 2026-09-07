<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SurveiDampak (Survei Dampak).
 *
 * Merepresentasikan entitas survei_dampak pada fase dampak SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class SurveiDampak extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'survei_dampak';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mitra_id',
        'proposal_id',
        'hasil',
        'testimoni'
    ];

    /**
     * Relasi ke Mitra Eksternal (belongsTo).
     */
    public function mitra(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
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
