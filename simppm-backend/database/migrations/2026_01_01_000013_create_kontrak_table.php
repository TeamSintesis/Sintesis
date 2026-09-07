<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel kontrak: Kontrak Penugasan
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel kontrak.
     */
    public function up(): void
    {
        Schema::create('kontrak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->string('nomor_sk', 50);
            $table->date('tanggal_kontrak');
            $table->decimal('nilai_kontrak', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel kontrak.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
