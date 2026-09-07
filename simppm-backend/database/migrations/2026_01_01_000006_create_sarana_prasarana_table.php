<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel sarana_prasarana: Sarana dan Prasarana
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel sarana_prasarana.
     */
    public function up(): void
    {
        Schema::create('sarana_prasarana', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sarpras', 150);
            $table->string('lokasi', 100)->nullable();
            $table->enum('status', ['tersedia', 'digunakan', 'rusak'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel sarana_prasarana.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarana_prasarana');
    }
};
