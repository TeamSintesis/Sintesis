<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi ini menambahkan kolom peran (role) dan tautan opsional ke entitas
// domain (dosen, mahasiswa, mitra) pada tabel users bawaan Laravel.
// Delapan nilai peran berikut mengikuti ringkasan aktor pada Use Case
// Diagram SIMPPM: admin_lppm, ketua_lppm, dosen, mahasiswa, reviewer,
// prodi_gkm, pimpinan, dan mitra_eksternal.
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk menambahkan kolom peran dan relasi identitas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('peran', [
                'admin_lppm',
                'ketua_lppm',
                'dosen',
                'mahasiswa',
                'reviewer',
                'prodi_gkm',
                'pimpinan',
                'mitra_eksternal',
            ])->default('dosen')->after('email');

            // Tautan opsional ke entitas domain agar kepemilikan data
            // (misalnya proposal milik dosen tertentu) dapat diverifikasi.
            $table->foreignId('dosen_id')->nullable()->after('peran')
                ->constrained('dosen')->nullOnDelete();
            $table->foreignId('mahasiswa_id')->nullable()->after('dosen_id')
                ->constrained('mahasiswa')->nullOnDelete();
            $table->foreignId('mitra_id')->nullable()->after('mahasiswa_id')
                ->constrained('mitra')->nullOnDelete();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus kolom yang ditambahkan.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dosen_id');
            $table->dropConstrainedForeignId('mahasiswa_id');
            $table->dropConstrainedForeignId('mitra_id');
            $table->dropColumn('peran');
        });
    }
};
