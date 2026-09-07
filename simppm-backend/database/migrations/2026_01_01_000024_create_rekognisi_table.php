<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel rekognisi: Rekognisi
// Dibuat otomatis dari kamus data SIMPPM (fase dampak).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel rekognisi.
     */
    public function up(): void
    {
        Schema::create('rekognisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->string('jenis', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('tahun');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel rekognisi.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekognisi');
    }
};
