<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User (pengguna sistem).
 *
 * Setiap pengguna memiliki satu peran (role) sesuai ringkasan aktor pada
 * Use Case Diagram SIMPPM: admin_lppm, ketua_lppm, dosen, mahasiswa,
 * reviewer, prodi_gkm, pimpinan, atau mitra_eksternal. Kolom dosen_id,
 * mahasiswa_id, dan mitra_id bersifat opsional dan digunakan untuk
 * memverifikasi kepemilikan data pada Policy (misalnya dosen hanya dapat
 * mengubah proposal miliknya sendiri).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Daftar peran (role) yang dikenali sistem.
     */
    public const PERAN_ADMIN_LPPM = 'admin_lppm';
    public const PERAN_KETUA_LPPM = 'ketua_lppm';
    public const PERAN_DOSEN = 'dosen';
    public const PERAN_MAHASISWA = 'mahasiswa';
    public const PERAN_REVIEWER = 'reviewer';
    public const PERAN_PRODI_GKM = 'prodi_gkm';
    public const PERAN_PIMPINAN = 'pimpinan';
    public const PERAN_MITRA_EKSTERNAL = 'mitra_eksternal';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'peran',
        'dosen_id',
        'mahasiswa_id',
        'mitra_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data Dosen apabila pengguna berperan sebagai dosen/reviewer.
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Relasi ke data Mahasiswa apabila pengguna berperan sebagai mahasiswa.
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke data Mitra apabila pengguna berperan sebagai mitra eksternal.
     */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Memeriksa apakah pengguna memiliki salah satu peran yang diberikan.
     *
     * @param string ...$peran
     */
    public function memilikiPeran(string ...$peran): bool
    {
        return in_array($this->peran, $peran, true);
    }
}
