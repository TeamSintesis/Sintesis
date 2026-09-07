<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel monev: Monitoring dan Evaluasi
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel monev.
     */
    public function up(): void
    {
        Schema::create('monev', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            // Reviewer/pemonev (nidn_reviewer)
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->enum('status', ['terjadwal', 'selesai'])->default('terjadwal');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel monev.
     */
    public function down(): void
    {
        Schema::dropIfExists('monev');
    }
};
