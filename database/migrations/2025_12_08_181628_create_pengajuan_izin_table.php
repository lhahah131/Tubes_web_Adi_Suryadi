<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['sakit', 'izin']); // Jenis pengajuan
            $table->date('tanggal_mulai'); // Tanggal mulai izin/sakit
            $table->date('tanggal_selesai'); // Tanggal selesai izin/sakit
            $table->text('keterangan'); // Alasan/keterangan
            $table->string('file_bukti')->nullable(); // Path file surat bukti
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_guru')->nullable(); // Catatan dari guru saat approve/reject
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Guru yang approve
            $table->timestamp('approved_at')->nullable(); // Waktu approve/reject
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
