<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel integrasi_kurikulum: Integrasi Kurikulum
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel integrasi_kurikulum.
     */
    public function up(): void
    {
        Schema::create('integrasi_kurikulum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->string('mata_kuliah', 100);
            $table->string('rps_tautan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel integrasi_kurikulum.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrasi_kurikulum');
    }
};
