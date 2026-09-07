<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel kerjasama: Kerja Sama
// Dibuat otomatis dari kamus data SIMPPM (fase dampak).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel kerjasama.
     */
    public function up(): void
    {
        Schema::create('kerjasama', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->string('ruang_lingkup', 200);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->enum('status', ['aktif', 'berakhir', 'diperpanjang'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel kerjasama.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama');
    }
};
