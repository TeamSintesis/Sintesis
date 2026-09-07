<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel proposal: Proposal Penelitian/PkM
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel proposal.
     */
    public function up(): void
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            // Dosen pengusul/ketua tim (nidn_pengusul)
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('skema_pendanaan_id')->constrained('skema_pendanaan')->cascadeOnDelete();
            $table->foreignId('peta_jalan_id')->nullable()->constrained('peta_jalan')->nullOnDelete();
            $table->string('judul', 250);
            $table->enum('jenis', ['penelitian', 'pkm']);
            $table->unsignedSmallInteger('tahun_usulan');
            $table->enum('status', ['draft', 'diajukan', 'direview', 'disetujui', 'ditolak'])->default('draft');
            $table->decimal('rab_total', 15, 2);
            // Atribut implementasi tambahan (tidak ada pada kamus data awal) untuk mendukung relasi <<extend>> Ajukan Klirens Etik pada use case Fase Proses.
            $table->boolean('berisiko_etik')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel proposal.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal');
    }
};
