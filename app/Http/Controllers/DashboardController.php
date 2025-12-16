<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengajuanIzin;
use App\Models\Absensi;
use App\Models\QrCode;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get authenticated user with type hint
     */
    private function getAuthUser(): ?User
    {
        return Auth::user();
    }

    public function index()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->role === 'guru') {
            return redirect()->route('guru.laporan'); // Atau guru.dashboard jika ada
        } else {
            // Sesuai request user: Masuk ke Dashboard Utama (Statistik), bukan Riwayat
            return redirect()->route('siswa.dashboard');
        }
    }

    // Guru Methods
    public function guruAbsensi()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('guru.absensi');
    }

    public function guruDashboard()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('guru.dashboard');
    }

    public function guruLaporan(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Filters
        $kelasId = $request->input('kelas_id');
        
        // Build Query
        $studentQuery = Siswa::with(['user', 'kelas', 'absensi']);
        
        if ($kelasId && $kelasId != 'all') {
            $studentQuery->where('kelas_id', $kelasId);
        }

        // Get students and stats
        $students = $studentQuery->get()
            ->map(function ($siswa) {
                // Count attendance by status
                $hadirCount = $siswa->absensi->where('status', 'hadir')->count();
                $sakitCount = $siswa->absensi->where('status', 'sakit')->count();
                $izinCount = $siswa->absensi->where('status', 'izin')->count();
                // Check multiple status for absent
                $absenCount = $siswa->absensi->filter(function($a) {
                    return in_array(strtolower($a->status), ['absen', 'alpa', 'alpha']); 
                })->count();

                return (object) [
                    'id' => $siswa->id,
                    'name' => $siswa->user->name,
                    'kelas' => $siswa->kelas->nama_kelas,
                    'nis' => $siswa->nis,
                    'hadir' => $hadirCount,
                    'sakit' => $sakitCount,
                    'izin' => $izinCount,
                    'absen' => $absenCount,
                ];
            });

        $allKelas = Kelas::all();

        return view('guru.laporan', compact('students', 'allKelas'));
    }

    public function guruLaporanDetail($id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $siswa = Siswa::find($id);
        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        $attendances = Absensi::where('siswa_id', $siswa->id)
            ->with(['kelas'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.laporan_detail', compact('siswa', 'attendances'));
    }

    public function guruAbsensiDestroy($id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return back()->with('success', 'Data absensi berhasil dihapus');
    }

    public function guruAbsensiClear($id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $siswa = Siswa::findOrFail($id);
        
        // Hapus semua data absensi siswa ini
        Absensi::where('siswa_id', $siswa->id)->delete();

        return back()->with('success', 'Semua riwayat absensi siswa berhasil dihapus');
    }

    public function guruGenerateQR(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // If request expects JSON (API call from page)
        if ($request->expectsJson() || $request->wantsJson()) {
            try {
                // Get guru data
                $guru = Guru::where('user_id', $user->id)->first();
                
                if (!$guru) {
                    return response()->json(['error' => 'Data guru tidak ditemukan'], 404);
                }

                // Generate unique QR code
                $kodeQr = 'QR-' . strtoupper(Str::random(16)) . '-' . time();
                
                // Set expiry time (15 minutes from now)
                $waktuBerlakuSampai = Carbon::now()->addMinutes(15);
                
                // For simplicity, use first kelas or create a default one
                $kelas = Kelas::first();
                if (!$kelas) {
                    $kelas = Kelas::create([
                        'nama_kelas' => '10A',
                        'wali_kelas' => $user->name
                    ]);
                }

                // Create QR Code record
                $qrCode = QrCode::create([
                    'kelas_id' => $kelas->id,
                    'kode_qr' => $kodeQr,
                    'waktu_dibuat' => Carbon::now(),
                    'waktu_berlaku_sampai' => $waktuBerlakuSampai,
                    'aktif' => true
                ]);

                return response()->json([
                    'success' => true,
                    'qr_code' => $kodeQr,
                    'qr_code_id' => $qrCode->id,
                    'kelas' => $kelas->nama_kelas,
                    'expired_at' => $waktuBerlakuSampai->format('Y-m-d H:i:s'),
                    'message' => 'QR Code berhasil dibuat!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal membuat QR Code: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('guru.generate_qr');
    }

    public function guruPengajuanIzin()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $pengajuan = PengajuanIzin::with(['user', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pending_count = PengajuanIzin::where('status', 'pending')->count();

        return view('guru.pengajuan_izin', compact('pengajuan', 'pending_count'));
    }

    public function guruPengajuanApprove(Request $request, $id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $pengajuan = PengajuanIzin::findOrFail($id);

        $pengajuan->update([
            'status' => 'approved',
            'catatan_guru' => $request->catatan_guru,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Buat record Absensi
        $siswa = Siswa::where('user_id', $pengajuan->user_id)->first();

        if ($siswa) {
            // Cek apakah sudah ada absen hari ini (optional, tapi good practice)
            // Di sini kita asumsikan approve = override atau tambah record
            // Untuk simplifikasi, kita create baru. 
            // Kalau mau safe, bisa pakai updateOrCreate atau cek existence dulu.
            
            // Format keterangan: [Mapel] Keterangan
            $keterangan = $pengajuan->keterangan;
            if (!empty($pengajuan->mata_pelajaran)) {
                $keterangan = "[{$pengajuan->mata_pelajaran}] " . $keterangan;
            }

            Absensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tanggal' => $pengajuan->tanggal_mulai, // atau now()
                'jam_masuk' => now()->format('H:i:s'),
                'status' => $pengajuan->jenis, // 'sakit' atau 'izin'
                'keterangan' => $keterangan,
                'qr_code_id' => null, // Tidak ada QR
            ]);
        }

        return redirect()->route('guru.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil disetujui dan dicatat ke riwayat absensi!');
    }

    public function guruPengajuanReject(Request $request, $id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $pengajuan = PengajuanIzin::findOrFail($id);

        $pengajuan->update([
            'status' => 'rejected',
            'catatan_guru' => $request->catatan_guru,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('guru.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil ditolak!');
    }

    public function guruPengajuanDestroy($id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $pengajuan = PengajuanIzin::findOrFail($id);
        $pengajuan->delete();

        return redirect()->route('guru.pengajuan_izin')
            ->with('success', 'Data pengajuan berhasil dihapus!');
    }



    // Siswa Methods
    public function siswaAbsensi()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.absensi');
    }

    public function siswaDashboard()
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
        $totalAbsen = 0; // Menggabungkan Alpa/Absen
        $persentaseKehadiran = 0;

        if ($siswa) {
            // Ambil semua data absensi siswa ini sekaligus untuk efisiensi
            // Kita lakukan filtering di PHP collection untuk fleksibilitas case-insensitive
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

            // Hitung total pertemuan (total record)
            $totalPertemuan = $allAbsensi->count();

            // Hitung Persentase: (Hadir / Total Pertemuan) * 100
            if ($totalPertemuan > 0) {
                $persentaseKehadiran = round(($totalHadir / $totalPertemuan) * 100);
            }
        }

        return view('siswa.dashboard', compact('totalHadir', 'totalSakit', 'totalIzin', 'totalAbsen', 'persentaseKehadiran'));
    }

    public function siswaRiwayat()
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

    public function siswaScanQR()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.scan_qr');
    }

    public function siswaPengajuanIzin()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.pengajuan_izin');
    }

    public function siswaPengajuanIzinStore(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Validasi input
        $validated = $request->validate([
            'jenis' => 'required|in:sakit,izin',
            'mata_pelajaran' => 'required|string',
            'keterangan' => 'required|string|min:10',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ], [
            'jenis.required' => 'Jenis pengajuan harus dipilih',
            'jenis.in' => 'Jenis pengajuan hanya boleh sakit atau izin',
            'mata_pelajaran.required' => 'Mata pelajaran harus diisi',
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

        // Simpan ke database (Default tanggal hari ini)
        PengajuanIzin::create([
            'user_id' => $user->id,
            'jenis' => $validated['jenis'],
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now(),
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
    public function siswaScanQRSubmit(Request $request)
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
                // Support keys panjang (legacy) & pendek (optimized)
                $kodeQr = $qrData['token'] ?? $qrData['t'] ?? $qrData['kode_qr'] ?? null;
            } else {
                // String biasa
                $kodeQr = $inputQr;
            }

            if (!$kodeQr) {
                return response()->json(['success' => false, 'error' => 'Format QR tidak valid'], 400);
            }

            // Check if QR code exists in DB
            // Catatan: Kita validasi based on Token/KodeQR yang ada di DB
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

            // Simpan Absensi
            // Kita ambil data tambahan dari QR jika ada, atau fallback ke default
            $absensi = Absensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $qrCode->kelas_id, // Dari DB relation
                'tanggal' => $today,
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'status' => 'hadir',
                'keterangan' => 'Hadir via QR',
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
            \Log::error('Error in siswaScanQRSubmit: ' . $e->getMessage());
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
