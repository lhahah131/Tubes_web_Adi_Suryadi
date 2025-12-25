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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruController extends Controller
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

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('guru.dashboard');
    }

    public function absensi()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('guru.absensi');
    }

    public function laporan(Request $request)
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

    public function laporanDetail($id)
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

    public function absensiDestroy($id)
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return back()->with('success', 'Data absensi berhasil dihapus');
    }

    public function absensiClear($id)
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

    public function generateQR(Request $request)
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

    public function pengajuanIzin()
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

    public function pengajuanApprove(Request $request, $id)
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
            // Format keterangan: [Mapel] Keterangan
            $keterangan = $pengajuan->keterangan;
            if (!empty($pengajuan->mata_pelajaran)) {
                $keterangan = "[{$pengajuan->mata_pelajaran}] " . $keterangan;
            }

            // Loop dari tanggal mulai sampai selesai
            $startDate = Carbon::parse($pengajuan->tanggal_mulai);
            $endDate = Carbon::parse($pengajuan->tanggal_selesai);

            // Jika tanggal selesai kurang dari mulai (fallback), set sama
            if ($endDate->lt($startDate)) {
                $endDate = $startDate->copy();
            }

            while ($startDate->lte($endDate)) {
                // Skip hari Minggu (0)
                if ($startDate->dayOfWeek !== Carbon::SUNDAY) {
                    Absensi::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => $startDate->format('Y-m-d'),
                        'jam_masuk' => now()->format('H:i:s'), // Jam saat di-approve
                        'status' => $pengajuan->jenis, // 'sakit' atau 'izin'
                        'keterangan' => $keterangan,
                        'qr_code_id' => null, // Tidak ada QR
                    ]);
                }
                $startDate->addDay();
            }
        }

        return redirect()->route('guru.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil disetujui dan dicatat ke riwayat absensi!');
    }

    public function pengajuanReject(Request $request, $id)
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

    public function pengajuanDestroy($id)
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
}
