<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model PencairanDana (Pencairan Dana).
 *
 * Merepresentasikan entitas pencairan_dana pada fase proses SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class PencairanDana extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'pencairan_dana';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kontrak_id',
        'termin',
        'jumlah',
        'tanggal',
        'status'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'jumlah' => 'decimal:2',
            'tanggal' => 'date'
    ];

    /**
     * Relasi ke Kontrak Penugasan (belongsTo).
     */
    public function kontrak(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kontrak::class, 'kontrak_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini (ditelusuri melalui
     * relasi kontrak -> proposal), untuk keperluan otorisasi berbasis
     * kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->kontrak?->proposal?->dosen_id;
    }
}
