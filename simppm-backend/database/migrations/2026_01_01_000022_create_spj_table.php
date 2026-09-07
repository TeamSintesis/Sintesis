<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel spj: Surat Pertanggungjawaban
// Dibuat otomatis dari kamus data SIMPPM (fase luaran).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel spj.
     */
    public function up(): void
    {
        Schema::create('spj', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_akhir_id')->constrained('laporan_akhir')->cascadeOnDelete();
            $table->decimal('jumlah_realisasi', 15, 2);
            $table->string('dokumen_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel spj.
     */
    public function down(): void
    {
        Schema::dropIfExists('spj');
    }
};
