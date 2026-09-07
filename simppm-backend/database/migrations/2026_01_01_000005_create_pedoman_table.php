<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel pedoman: Pedoman dan Kode Etik
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel pedoman.
     */
    public function up(): void
    {
        Schema::create('pedoman', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 50);
            $table->string('versi', 20);
            $table->date('tanggal_berlaku');
            $table->string('dokumen_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel pedoman.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedoman');
    }
};
