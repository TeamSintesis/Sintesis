<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel peta_jalan: Peta Jalan Penelitian/PkM
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel peta_jalan.
     */
    public function up(): void
    {
        Schema::create('peta_jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renstra_id')->constrained('renstra')->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('bidang_keilmuan', 150);
            $table->unsignedSmallInteger('tahun');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel peta_jalan.
     */
    public function down(): void
    {
        Schema::dropIfExists('peta_jalan');
    }
};
