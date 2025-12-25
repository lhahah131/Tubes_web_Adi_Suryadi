<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengajuanIzin;
use App\Models\Absensi;
use App\Models\QrCode;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Get authenticated user with type hint
     */
    private function getAuthUser(): ?User
    {
        return Auth::user();
    }

    public function dashboard()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get Siswa Data
        $siswa = Siswa::where('user_id', $user->id)->first();

        // Default values
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAbsen = 0; 
        $persentaseKehadiran = 0;

        if ($siswa) {
            $allAbsensi = Absensi::where('siswa_id', $siswa->id)->get();

            foreach ($allAbsensi as $absen) {
                // Normalisasi status ke lowercase untuk comparison
                $status = strtolower($absen->status);

                if ($status === 'hadir') {
                    $totalHadir++;
                } elseif ($status === 'sakit') {
                    $totalSakit++;
                } elseif ($status === 'izin') {
                    $totalIzin++;
                } elseif (in_array($status, ['absen', 'alpa', 'alpha'])) {
                    $totalAbsen++;
                }
            }

            // Hitung total pertemuan
            $totalPertemuan = $allAbsensi->count();

            // Hitung Persentase
            if ($totalPertemuan > 0) {
                $persentaseKehadiran = round(($totalHadir / $totalPertemuan) * 100);
            }
        }

        return view('siswa.dashboard', compact('totalHadir', 'totalSakit', 'totalIzin', 'totalAbsen', 'persentaseKehadiran'));
    }

    public function absensi()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.absensi');
    }

    public function riwayat(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Get siswa data
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return view('siswa.riwayat', ['attendances' => collect([])]);
        }

        // Get attendance history for this student
        $attendances = Absensi::where('siswa_id', $siswa->id)
            ->with(['kelas', 'qrCode'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('siswa.riwayat', compact('attendances'));
    }

    public function scanQR()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.scan_qr');
    }

    public function pengajuanIzin()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.pengajuan_izin');
    }

    public function pengajuanIzinStore(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Validasi input
        $validated = $request->validate([
            'jenis' => 'required|in:sakit,izin',
            'mata_pelajaran' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ], [
            'jenis.required' => 'Jenis pengajuan harus dipilih',
            'jenis.in' => 'Jenis pengajuan hanya boleh sakit atau izin',
            'mata_pelajaran.required' => 'Mata pelajaran harus diisi',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'file_bukti.mimes' => 'File harus berformat JPG, PNG, atau PDF',
            'file_bukti.max' => 'Ukuran file maksimal 2MB',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pengajuan_izin', $fileName, 'public');
        }

        // Simpan ke database
        PengajuanIzin::create([
            'user_id' => $user->id,
            'jenis' => $validated['jenis'],
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'keterangan' => $validated['keterangan'],
            'file_bukti' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('siswa.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan guru.');
    }

    /**
     * Handle QR Code Scan and Submit Attendance (MOBILE/API)
     */
    public function scanQRSubmit(Request $request)
    {
        try {
            $user = $this->getAuthUser();

            if (!$user || $user->role !== 'siswa') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'qr_code' => 'required|string'
            ]);

            // Parse QR Code data
            $qrData = null;
            $kodeQr = null;

            // Coba decode jika input adalah JSON string
            $inputQr = $validated['qr_code'];
            
            if ($this->isJson($inputQr)) {
                $qrData = json_decode($inputQr, true);
                $kodeQr = $qrData['token'] ?? $qrData['t'] ?? $qrData['kode_qr'] ?? null;
            } else {
                $kodeQr = $inputQr;
            }

            if (!$kodeQr) {
                return response()->json(['success' => false, 'error' => 'Format QR tidak valid'], 400);
            }

            // Check if QR code exists in DB
            $qrCode = QrCode::where('kode_qr', $kodeQr)
                ->where('aktif', true)
                ->first();

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'error' => 'QR Code tidak valid atau sudah tidak aktif'
                ], 400);
            }

            // Check expiry
            if ($qrCode->waktu_berlaku_sampai && Carbon::now()->greaterThan($qrCode->waktu_berlaku_sampai)) {
                return response()->json([
                    'success' => false, 
                    'error' => 'QR Code sudah kadaluarsa'
                ], 400);
            }

            // Get Siswa
            $siswa = Siswa::where('user_id', $user->id)->first();
            if (!$siswa) {
                return response()->json(['success' => false, 'error' => 'Data siswa tidak ditemukan'], 404);
            }

            // Check Duplicate (Absen hari ini)
            $today = Carbon::today();
            $existingAbsensi = Absensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingAbsensi) {
                return response()->json([
                    'success' => false,
                    'error'  => 'Sudah absen jam ' . Carbon::parse($existingAbsensi->jam_masuk)->format('H:i'),
                    'data' => [
                        'jam' => $existingAbsensi->jam_masuk,
                        'status' => $existingAbsensi->status
                    ]
                ], 400);
            }

            // Cari Jadwal Pelajaran Aktif (Untuk mengisi keterangan mapel)
            $hariIni = Carbon::now()->locale('id')->isoFormat('dddd'); // Senin, Selasa...
            $jamSekarang = Carbon::now()->format('H:i:s');
            
            // Perbaiki nama hari jika locale Inggris (fallback sederhana)
            $mapHari = [
                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 
                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
            ];
            if (isset($mapHari[$hariIni])) {
                $hariIni = $mapHari[$hariIni];
            }

            // Cari jadwal yang cocok dengan kelas, hari, dan jam saat ini
            $jadwal = \App\Models\JadwalPelajaran::where('kelas_id', $qrCode->kelas_id)
                ->where('hari', $hariIni)
                ->whereHas('jamPelajaran', function($q) use ($jamSekarang) {
                    $q->where('jam_mulai', '<=', $jamSekarang)
                      ->where('jam_selesai', '>=', $jamSekarang);
                })
                ->with('guru') // Load guru untuk ambil nama mapel
                ->first();

            $keterangan = 'Hadir via QR';
            
            if ($jadwal && $jadwal->guru) {
                // Tambahkan nama mata pelajaran ke keterangan
                $keterangan .= ' - ' . $jadwal->guru->mata_pelajaran;
            } else {
                // Optional: Jika tidak ada jadwal spesifik, cek apakah masih dalam jam sekolah
                // Atau biarkan default
            }

            // Simpan Absensi
            $absensi = Absensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $qrCode->kelas_id,
                'tanggal' => $today,
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'status' => 'hadir',
                'keterangan' => $keterangan,
                'qr_code_id' => $qrCode->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil!',
                'data' => [
                    'nama' => $user->name,
                    'jam' => $absensi->jam_masuk,
                    'status' => 'hadir'
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to check if string is valid JSON
     */
    private function isJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
