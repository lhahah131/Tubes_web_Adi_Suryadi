<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            return redirect()->route('guru.laporan');
        } else {
            return redirect()->route('siswa.riwayat');
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

    public function guruLaporan()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('guru.laporan');
    }

    public function guruGenerateQR()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
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

        return redirect()->route('guru.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil disetujui!');
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

        return view('siswa.dashboard');
    }

    public function siswaRiwayat()
    {
        $user = $this->getAuthUser();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('siswa.riwayat');
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
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10',
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ], [
            'jenis.required' => 'Jenis pengajuan harus dipilih',
            'jenis.in' => 'Jenis pengajuan hanya boleh sakit atau izin',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai',
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
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'keterangan' => $validated['keterangan'],
            'file_bukti' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('siswa.pengajuan_izin')
            ->with('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan guru.');
    }
}
