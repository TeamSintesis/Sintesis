<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel hki: Kekayaan Intelektual
// Dibuat otomatis dari kamus data SIMPPM (fase luaran).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel hki.
     */
    public function up(): void
    {
        Schema::create('hki', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->enum('jenis', ['paten', 'hak_cipta', 'desain_industri', 'merek', 'lainnya']);
            $table->enum('status', ['diajukan', 'diperiksa_substantif', 'terbit', 'ditolak'])->default('diajukan');
            $table->string('nomor_sertifikat', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel hki.
     */
    public function down(): void
    {
        Schema::dropIfExists('hki');
    }
};
