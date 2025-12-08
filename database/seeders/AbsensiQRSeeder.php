<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;

class AbsensiQRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Kelas
        $kelasXA = Kelas::create([
            'nama_kelas' => 'X-A',
            'wali_kelas' => 'Ibu Ratna Dewi'
        ]);

        $kelasXB = Kelas::create([
            'nama_kelas' => 'X-B',
            'wali_kelas' => 'Pak Ahmad Pratama'
        ]);

        // Create Jam Pelajaran
        $jam1 = JamPelajaran::create([
            'jam_ke' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:45:00'
        ]);

        $jam2 = JamPelajaran::create([
            'jam_ke' => 2,
            'jam_mulai' => '07:45:00',
            'jam_selesai' => '08:30:00'
        ]);

        $jam3 = JamPelajaran::create([
            'jam_ke' => 3,
            'jam_mulai' => '08:30:00',
            'jam_selesai' => '09:15:00'
        ]);

        // Create Users & Siswa
        $userSiswa1 = User::create([
            'name' => 'Budi Santoso',
            'username' => 'siswa1',
            'email' => 'siswa1@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa'
        ]);

        Siswa::create([
            'user_id' => $userSiswa1->id,
            'nis' => '001',
            'nomor_induk' => 'BDS001',
            'kelas_id' => $kelasXA->id,
            'tanggal_lahir' => '2008-05-15',
            'alamat' => 'Jl. Merdeka No. 10',
            'nomor_telepon' => '082123456789'
        ]);

        $userSiswa2 = User::create([
            'name' => 'Siti Nurhaliza',
            'username' => 'siswa2',
            'email' => 'siswa2@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa'
        ]);

        Siswa::create([
            'user_id' => $userSiswa2->id,
            'nis' => '002',
            'nomor_induk' => 'SNH002',
            'kelas_id' => $kelasXA->id,
            'tanggal_lahir' => '2008-07-22',
            'alamat' => 'Jl. Sudirman No. 5',
            'nomor_telepon' => '082223456789'
        ]);

        $userSiswa3 = User::create([
            'name' => 'Ahmad Ridho',
            'username' => 'siswa3',
            'email' => 'siswa3@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa'
        ]);

        Siswa::create([
            'user_id' => $userSiswa3->id,
            'nis' => '003',
            'nomor_induk' => 'ARD003',
            'kelas_id' => $kelasXB->id,
            'tanggal_lahir' => '2008-03-10',
            'alamat' => 'Jl. Gatot Subroto No. 15',
            'nomor_telepon' => '082323456789'
        ]);

        // Create Users & Guru
        $userGuru1 = User::create([
            'name' => 'Ibu Ratna Dewi',
            'username' => 'guru1',
            'email' => 'guru1@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru'
        ]);

        Guru::create([
            'user_id' => $userGuru1->id,
            'nip' => '198505102010011001',
            'mata_pelajaran' => 'Matematika',
            'tanggal_lahir' => '1985-05-10',
            'alamat' => 'Jl. Kebon Sirih No. 20',
            'nomor_telepon' => '081123456789'
        ]);

        $userGuru2 = User::create([
            'name' => 'Pak Ahmad Pratama',
            'username' => 'guru2',
            'email' => 'guru2@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru'
        ]);

        Guru::create([
            'user_id' => $userGuru2->id,
            'nip' => '197805122005011002',
            'mata_pelajaran' => 'Bahasa Indonesia',
            'tanggal_lahir' => '1978-05-12',
            'alamat' => 'Jl. Hayam Wuruk No. 8',
            'nomor_telepon' => '081223456789'
        ]);

        // Create Jadwal Pelajaran
        JadwalPelajaran::create([
            'guru_id' => 1,
            'kelas_id' => $kelasXA->id,
            'jam_pelajaran_id' => $jam1->id,
            'hari' => 'Senin'
        ]);

        JadwalPelajaran::create([
            'guru_id' => 2,
            'kelas_id' => $kelasXB->id,
            'jam_pelajaran_id' => $jam2->id,
            'hari' => 'Selasa'
        ]);

        // Create Absensi Sample Data
        $today = now()->toDateString();

        Absensi::create([
            'siswa_id' => 1,
            'kelas_id' => $kelasXA->id,
            'tanggal' => $today,
            'jam_masuk' => '07:15',
            'status' => 'hadir',
            'keterangan' => null
        ]);

        Absensi::create([
            'siswa_id' => 2,
            'kelas_id' => $kelasXA->id,
            'tanggal' => $today,
            'jam_masuk' => '07:10',
            'status' => 'hadir',
            'keterangan' => null
        ]);

        Absensi::create([
            'siswa_id' => 3,
            'kelas_id' => $kelasXB->id,
            'tanggal' => $today,
            'jam_masuk' => null,
            'status' => 'sakit',
            'keterangan' => 'Sakit demam'
        ]);
    }
}
