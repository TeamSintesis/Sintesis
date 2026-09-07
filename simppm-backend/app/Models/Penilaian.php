<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Penilaian (Penilaian Proposal).
 *
 * Merepresentasikan entitas penilaian pada fase proses SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Penilaian extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'penilaian';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'dosen_id',
        'skor',
        'komentar',
        'status_finalisasi',
        'tanggal'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'skor' => 'decimal:2',
            'status_finalisasi' => 'boolean',
            'tanggal' => 'date'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Relasi ke Dosen/Peneliti (belongsTo).
     */
    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi proposal), untuk keperluan otorisasi berbasis kepemilikan
     * pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->proposal?->dosen_id;
    }
}
