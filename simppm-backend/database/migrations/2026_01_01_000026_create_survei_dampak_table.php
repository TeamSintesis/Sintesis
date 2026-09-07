<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel survei_dampak: Survei Dampak
// Dibuat otomatis dari kamus data SIMPPM (fase dampak).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel survei_dampak.
     */
    public function up(): void
    {
        Schema::create('survei_dampak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained('proposal')->nullOnDelete();
            $table->text('hasil')->nullable();
            $table->text('testimoni')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel survei_dampak.
     */
    public function down(): void
    {
        Schema::dropIfExists('survei_dampak');
    }
};
