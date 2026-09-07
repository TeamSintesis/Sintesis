<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel renstra: Rencana Strategis
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel renstra.
     */
    public function up(): void
    {
        Schema::create('renstra', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['penelitian', 'pkm']);
            $table->unsignedSmallInteger('periode_mulai');
            $table->unsignedSmallInteger('periode_akhir');
            $table->string('dokumen_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel renstra.
     */
    public function down(): void
    {
        Schema::dropIfExists('renstra');
    }
};
