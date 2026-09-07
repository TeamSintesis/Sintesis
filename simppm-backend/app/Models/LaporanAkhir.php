<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model LaporanAkhir (Laporan Akhir).
 *
 * Merepresentasikan entitas laporan_akhir pada fase luaran SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class LaporanAkhir extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'laporan_akhir';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'dokumen_url',
        'tanggal_submit',
        'status_verifikasi'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'tanggal_submit' => 'date'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Surat Pertanggungjawaban terkait (hasMany).
     */
    public function spjList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Spj::class, 'laporan_akhir_id');
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
