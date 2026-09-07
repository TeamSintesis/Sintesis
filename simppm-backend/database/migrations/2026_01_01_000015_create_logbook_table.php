<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel logbook: Logbook Kemajuan
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel logbook.
     */
    public function up(): void
    {
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->string('periode', 20);
            $table->text('isi_kemajuan');
            $table->date('tanggal_isi');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel logbook.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook');
    }
};
