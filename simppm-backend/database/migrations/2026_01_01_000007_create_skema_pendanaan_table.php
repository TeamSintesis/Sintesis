<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel skema_pendanaan: Skema Pendanaan
// Dibuat otomatis dari kamus data SIMPPM (fase masukan).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel skema_pendanaan.
     */
    public function up(): void
    {
        Schema::create('skema_pendanaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_skema', 100);
            $table->enum('jenis', ['internal', 'eksternal']);
            $table->decimal('plafon_dana', 15, 2);
            $table->string('sumber_dana', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel skema_pendanaan.
     */
    public function down(): void
    {
        Schema::dropIfExists('skema_pendanaan');
    }
};
