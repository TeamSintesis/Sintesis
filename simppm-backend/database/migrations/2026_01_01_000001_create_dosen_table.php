<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel dosen: Dosen/Peneliti
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel dosen.
     */
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('nidn', 10)->unique();
            $table->string('nama', 100);
            $table->string('jabatan_fungsional', 50)->nullable();
            $table->string('bidang_kepakaran', 150)->nullable();
            $table->string('id_sinta', 20)->nullable();
            $table->string('scopus_id', 20)->nullable();
            $table->string('orcid', 25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel dosen.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
