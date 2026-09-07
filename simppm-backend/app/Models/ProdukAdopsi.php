<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ProdukAdopsi (Produk/Purwarupa Adopsi).
 *
 * Merepresentasikan entitas produk_adopsi pada fase luaran SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class ProdukAdopsi extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'produk_adopsi';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proposal_id',
        'mitra_id',
        'nama_produk',
        'bukti_adopsi'
    ];

    /**
     * Relasi ke Proposal Penelitian/PkM (belongsTo).
     */
    public function proposal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Relasi ke Mitra Eksternal (belongsTo).
     */
    public function mitra(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
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

    /**
     * Kembalikan ID mitra eksternal pemilik data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikMitraId(): ?int
    {
        return $this->mitra_id;
    }
}
