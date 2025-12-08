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
        // Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas')->unique();
            $table->string('wali_kelas')->nullable();
            $table->timestamps();
        });

        // Tabel Siswa
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nis')->unique();
            $table->string('nomor_induk')->nullable();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->timestamps();
        });

        // Tabel Guru
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('mata_pelajaran')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->timestamps();
        });

        // Tabel QR Code
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('kode_qr')->unique();
            $table->timestamp('waktu_dibuat');
            $table->timestamp('waktu_berlaku_sampai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tabel Absensi
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->enum('status', ['hadir', 'absen', 'sakit', 'izin'])->default('absen');
            $table->string('keterangan')->nullable();
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->onDelete('set null');
            $table->timestamps();
        });

        // Tabel Jam Pelajaran
        Schema::create('jam_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->integer('jam_ke');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });

        // Tabel Jadwal Pelajaran
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('jam_pelajaran_id')->constrained('jam_pelajaran')->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])->nullable();
            $table->timestamps();
        });

        // Tabel Laporan Absensi
        Schema::create('laporan_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('total_hari_efektif');
            $table->timestamps();
        });

        // Tabel Detail Laporan
        Schema::create('detail_laporan_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_absensi_id')->constrained('laporan_absensi')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->integer('hadir');
            $table->integer('absen');
            $table->integer('sakit');
            $table->integer('izin');
            $table->decimal('persentase_kehadiran', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_laporan_absensi');
        Schema::dropIfExists('laporan_absensi');
        Schema::dropIfExists('jadwal_pelajaran');
        Schema::dropIfExists('jam_pelajaran');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
    }
};
