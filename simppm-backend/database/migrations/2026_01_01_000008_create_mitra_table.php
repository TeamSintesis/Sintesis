<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel mitra: Mitra Eksternal
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel mitra.
     */
    public function up(): void
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mitra', 150);
            $table->enum('jenis_mitra', ['industri', 'pemda', 'masyarakat', 'pt_lain']);
            $table->string('kontak', 100)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel mitra.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
