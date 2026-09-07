<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel penilaian: Penilaian Proposal
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel penilaian.
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            // Reviewer (nidn_reviewer)
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->nullable();
            $table->text('komentar')->nullable();
            $table->boolean('status_finalisasi')->default(false);
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel penilaian.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
