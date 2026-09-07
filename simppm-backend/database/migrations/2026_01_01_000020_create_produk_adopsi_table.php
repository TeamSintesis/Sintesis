<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel produk_adopsi: Produk/Purwarupa Adopsi
// Dibuat otomatis dari kamus data SIMPPM (fase luaran).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel produk_adopsi.
     */
    public function up(): void
    {
        Schema::create('produk_adopsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->foreignId('mitra_id')->nullable()->constrained('mitra')->nullOnDelete();
            $table->string('nama_produk', 150);
            $table->string('bukti_adopsi', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel produk_adopsi.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_adopsi');
    }
};
