<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Proposal (Proposal Penelitian/PkM).
 *
 * Merepresentasikan entitas proposal pada fase proses SIMPPM,
 * sesuai kamus data yang telah disusun sebelumnya.
 */
class Proposal extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit agar konsisten dengan kamus data (bentuk tunggal,
     * bukan bentuk jamak bawaan Eloquent).
     *
     * @var string
     */
    protected $table = 'proposal';

    /**
     * Kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dosen_id',
        'skema_pendanaan_id',
        'peta_jalan_id',
        'judul',
        'jenis',
        'tahun_usulan',
        'status',
        'rab_total',
        'berisiko_etik'
    ];

    /**
     * Atribut yang perlu dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'rab_total' => 'decimal:2',
            'berisiko_etik' => 'boolean'
    ];

    /**
     * Relasi ke Dosen/Peneliti (belongsTo).
     */
    public function pengusul(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Relasi ke Skema Pendanaan (belongsTo).
     */
    public function skemaPendanaan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SkemaPendanaan::class, 'skema_pendanaan_id');
    }

    /**
     * Relasi ke Peta Jalan Penelitian/PkM (belongsTo).
     */
    public function petaJalan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PetaJalan::class, 'peta_jalan_id');
    }

    /**
     * Relasi ke daftar Anggota Tim Proposal terkait (hasMany).
     */
    public function anggotaTimList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnggotaTim::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Penilaian Proposal terkait (hasMany).
     */
    public function penilaianList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penilaian::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Klirens Etik terkait (hasMany).
     */
    public function klirensEtikList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KlirensEtik::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Kontrak Penugasan terkait (hasMany).
     */
    public function kontrakList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Kontrak::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Logbook Kemajuan terkait (hasMany).
     */
    public function logbookList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Logbook::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Monitoring dan Evaluasi terkait (hasMany).
     */
    public function monevList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Monev::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Integrasi Kurikulum terkait (hasMany).
     */
    public function integrasiKurikulumList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IntegrasiKurikulum::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Publikasi terkait (hasMany).
     */
    public function publikasiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Publikasi::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Kekayaan Intelektual terkait (hasMany).
     */
    public function hkiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Hki::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Produk/Purwarupa Adopsi terkait (hasMany).
     */
    public function produkAdopsiList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProdukAdopsi::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Laporan Akhir terkait (hasMany).
     */
    public function laporanAkhirList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LaporanAkhir::class, 'proposal_id');
    }

    /**
     * Relasi ke daftar Survei Dampak terkait (hasMany).
     */
    public function surveiDampakList(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SurveiDampak::class, 'proposal_id');
    }

    /**
     * Kembalikan ID dosen pemilik/pengusul data ini, untuk keperluan
     * otorisasi berbasis kepemilikan pada Policy.
     */
    public function pemilikDosenId(): ?int
    {
        return $this->dosen_id;
    }
}
