<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel anggota_tim: Anggota Tim Proposal
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel anggota_tim.
     */
    public function up(): void
    {
        Schema::create('anggota_tim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('mahasiswa_id')->nullable()->constrained('mahasiswa')->nullOnDelete();
            $table->string('peran', 50);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel anggota_tim.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_tim');
    }
};
