<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel mahasiswa: Mahasiswa
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel mahasiswa.
     */
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('nim', 15)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel mahasiswa.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
