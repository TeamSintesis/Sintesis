<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi tabel pencairan_dana: Pencairan Dana
// Dibuat otomatis dari kamus data SIMPPM (fase proses).
return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel pencairan_dana.
     */
    public function up(): void
    {
        Schema::create('pencairan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_id')->constrained('kontrak')->cascadeOnDelete();
            $table->integer('termin');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal')->nullable();
            $table->enum('status', ['dijadwalkan', 'dicairkan', 'tertunda'])->default('dijadwalkan');
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi dengan menghapus tabel pencairan_dana.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan_dana');
    }
};
