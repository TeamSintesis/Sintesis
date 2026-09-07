<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel publikasi: Publikasi
// Dibuat otomatis dari kamus data SIMPPM (fase luaran).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel publikasi.
     */
    public function up(): void
    {
        Schema::create('publikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->string('judul', 250);
            $table->string('jurnal_prosiding', 150)->nullable();
            $table->enum('indeksasi', ['scopus', 'sinta_1', 'sinta_2', 'sinta_3', 'sinta_4', 'sinta_5', 'sinta_6', 'nasional_non_sinta']);
            $table->unsignedSmallInteger('tahun');
            $table->string('doi', 100)->nullable();
            $table->string('lisensi', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel publikasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('publikasi');
    }
};
