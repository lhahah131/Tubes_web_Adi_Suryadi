@extends('layouts.app')

@section('content')
@php
    // LOGIC VIEW (Agar tidak merubah Controller)
    $today = \Carbon\Carbon::today();
    
    // Hitung Pending Izin Manual di View
    $pendingIzinCount = \App\Models\PengajuanIzin::where('status', 'pending')->count();

    // Cari QR Code Aktif Terakhir
    $activeQrCode = \App\Models\QrCode::where('aktif', true)
                     ->where('waktu_berlaku_sampai', '>', \Carbon\Carbon::now())
                     ->count() > 0; // Return true if valid QR exists
@endphp

<div class="max-w-5xl mx-auto p-4 md:p-8 space-y-8">

    <!-- 1. Header with Compact Layout -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Selamat Datang, {{ Auth::user()->name }}.</h1>
            <p class="text-sm md:text-base text-gray-400 mt-1">Kelola kelas dan absensi siswa dengan efisien.</p>
        </div>
        <div class="flex items-center gap-3">
           <a href="{{ route('guru.pengajuan_izin') }}" class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 transition shadow-sm group">
                <i class="fas fa-inbox text-lg group-hover:scale-110 transition"></i>
                @if($pendingIzinCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white shadow-sm animate-bounce">
                        {{ $pendingIzinCount }}
                    </span>
                @endif
           </a>
           <div class="h-11 w-11 rounded-xl bg-gray-900 border-2 border-gray-900 text-white flex items-center justify-center text-base font-bold shadow-md">
                {{ substr(Auth::user()->name, 0, 1) }}
           </div>
        </div>
    </div>

    <!-- 2. Main Actions Grid (Responsive 2 Cols) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Generate QR Card -->
        <div class="group relative overflow-hidden rounded-3xl border border-gray-100 bg-white p-6 md:p-8 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] transition-all">
            <div class="flex items-start justify-between mb-6">
                <div class="h-14 w-14 rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 flex items-center justify-center transition-transform group-hover:rotate-6 group-hover:scale-110">
                    <i class="fas fa-qrcode text-2xl"></i>
                </div>
                <!-- Status Badge -->
                @if($activeQrCode)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 pl-2 pr-3 py-1 text-xs font-bold text-green-700 ring-1 ring-green-100">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Sesi Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">
                        Tidak Aktif
                    </span>
                @endif
            </div>

            <h3 class="text-xl font-bold text-gray-900 mb-2">Scan Absensi Kelas</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Mulai sesi baru dengan membuat QR Code unik untuk discan oleh siswa di kelas.</p>

            <a href="{{ route('guru.absensi') }}" class="inline-flex items-center justify-center w-full gap-2 rounded-xl bg-gray-900 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-gray-200 transition-all hover:bg-black hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
                {{ $activeQrCode ? 'Lihat QR Code Aktif' : 'Buat Sesi Baru' }}
                <i class="fas fa-arrow-right text-xs opacity-70 ml-1"></i>
            </a>
        </div>

        <!-- Approval Card -->
        <div class="group relative overflow-hidden rounded-3xl border border-gray-100 bg-white p-6 md:p-8 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] transition-all">
            <div class="flex items-start justify-between mb-6">
                <div class="h-14 w-14 rounded-2xl bg-purple-50 text-purple-600 ring-1 ring-purple-100 flex items-center justify-center transition-transform group-hover:rotate-6 group-hover:scale-110">
                    <i class="fas fa-envelope-open-text text-2xl"></i>
                </div>
                @if($pendingIzinCount > 0)
                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-600 ring-1 ring-red-100">
                        {{ $pendingIzinCount }} Menunggu
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-600 ring-1 ring-green-100">
                        <i class="fas fa-check mr-1.5 text-[10px]"></i> All Clear
                    </span>
                @endif
            </div>

            <h3 class="text-xl font-bold text-gray-900 mb-2">Persetujuan Izin</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">Tinjau dan proses surat keterangan sakit atau izin yang diajukan oleh siswa.</p>

            <a href="{{ route('guru.pengajuan_izin') }}" class="inline-flex items-center justify-center w-full gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3.5 text-sm font-bold text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 active:scale-[0.98]">
                Buka Inbox Pengajuan
            </a>
        </div>
    </div>

    <!-- 3. Ringkasan Statistik (Wide Card) -->
    <div class="rounded-3xl border border-gray-100 bg-white p-6 md:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-bold text-gray-900">Performa Kehadiran Hari Ini</h3>
            <a href="{{ route('guru.laporan') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition flex items-center gap-1 group">
                Analisis Lengkap <i class="fas fa-chevron-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
            <!-- Item -->
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Hadir</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($attendanceSummary['hadir'] ?? 0, 0) }}%</span>
                </div>
                <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-green-500 rounded-full" style="width: {{ $attendanceSummary['hadir'] ?? 0 }}%"></div>
                </div>
            </div>

            <!-- Item -->
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Izin</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($attendanceSummary['izin'] ?? 0, 0) }}%</span>
                </div>
                <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-blue-500 rounded-full" style="width: {{ $attendanceSummary['izin'] ?? 0 }}%"></div>
                </div>
            </div>

            <!-- Item -->
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sakit</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($attendanceSummary['sakit'] ?? 0, 0) }}%</span>
                </div>
                <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-yellow-400 rounded-full" style="width: {{ $attendanceSummary['sakit'] ?? 0 }}%"></div>
                </div>
            </div>

            <!-- Item -->
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Absen</span>
                    <span class="text-xl font-bold text-gray-900">{{ number_format($attendanceSummary['absen'] ?? 0, 0) }}%</span>
                </div>
                <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-red-500 rounded-full" style="width: {{ $attendanceSummary['absen'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection