<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KlirensEtik (Klirens Etik).
 *
 * Merepresentasikan entitas klirens_etik pada fase proses SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class KlirensEtik extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'klirens_etik';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'status',
        'nomor_sertifikat',
        'tanggal_terbit'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'tanggal_terbit' => 'date'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
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
