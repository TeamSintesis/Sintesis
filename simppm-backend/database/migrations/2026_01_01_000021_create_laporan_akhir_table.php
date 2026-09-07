<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel laporan_akhir: Laporan Akhir
// Dibuat otomatis dari kamus data SIMPPM (fase luaran).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel laporan_akhir.
     */
    public function up(): void
    {
        Schema::create('laporan_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->string('dokumen_url', 255);
            $table->date('tanggal_submit');
            $table->enum('status_verifikasi', ['belum', 'terverifikasi'])->default('belum');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel laporan_akhir.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_akhir');
    }
};
