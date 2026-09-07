<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel sitasi: Sitasi
// Dibuat otomatis dari kamus data SIMPPM (fase dampak).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel sitasi.
     */
    public function up(): void
    {
        Schema::create('sitasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publikasi_id')->nullable()->constrained('publikasi')->nullOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->integer('jumlah_sitasi')->default(0);
            $table->unsignedSmallInteger('tahun');
            $table->enum('sumber', ['google_scholar', 'sinta', 'scopus']);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel sitasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitasi');
    }
};
