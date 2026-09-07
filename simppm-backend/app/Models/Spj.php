<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Spj (Surat Pertanggungjawaban).
 *
 * Merepresentasikan entitas spj pada fase luaran SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Spj extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'spj';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'laporan_akhir_id',
        'jumlah_realisasi',
        'dokumen_url'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'jumlah_realisasi' => 'decimal:2'
    ];

    /**
     * Relasi ke Laporan Akhir (belongsTo).
     */
    public function laporanAkhir(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LaporanAkhir::class, 'laporan_akhir_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi laporanAkhir -> proposal), untuk keperluan otorisasi
     * berbasis kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->laporanAkhir?->proposal?->dosen_id;
    }
}
