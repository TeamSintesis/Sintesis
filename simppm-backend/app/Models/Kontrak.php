<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Kontrak (Kontrak Penugasan).
 *
 * Merepresentasikan entitas kontrak pada fase proses SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Kontrak extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'kontrak';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'nomor_sk',
        'tanggal_kontrak',
        'nilai_kontrak'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'tanggal_kontrak' => 'date',
            'nilai_kontrak' => 'decimal:2'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Pencairan Dana terkait (hasMany).
     */
    public function pencairanDanaList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PencairanDana::class, 'kontrak_id');
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
