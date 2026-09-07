<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel klirens_etik: Klirens Etik
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel klirens_etik.
     */
    public function up(): void
    {
        Schema::create('klirens_etik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->enum('status', ['diajukan', 'ditinjau', 'disetujui', 'ditolak'])->default('diajukan');
            $table->string('nomor_sertifikat', 50)->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel klirens_etik.
     */
    public function down(): void
    {
        Schema::dropIfExists('klirens_etik');
    }
};
