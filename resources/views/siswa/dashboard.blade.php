@extends('layouts.app')

@section('content')
@php
    // LOGIC VIEW (Agar tidak merubah Controller)
    $siswa = Auth::user()->siswa;
    $today = \Carbon\Carbon::today();
    $sudahAbsen = \App\Models\Absensi::where('siswa_id', $siswa->id)
                    ->whereDate('tanggal', $today)
                    ->first();
    
    // Ambil 3 riwayat terakhir
    $riwayatTerakhir = \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();
@endphp

<div class="max-w-5xl mx-auto p-4 md:p-8 space-y-8">
    
    <!-- 1. Header Sederhana -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1 hidden md:block">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Halo, {{ explode(' ', Auth::user()->name)[0] }} 👋</h1>
            <p class="text-sm md:text-base text-gray-400 mt-1">Siap untuk aktivitas hari ini?</p>
        </div>
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200 shadow-sm">
            <i class="fas fa-user text-gray-600 md:text-lg"></i>
        </div>
    </div>

    <!-- GRID LAYOUT (Responsive: 1 col mobile, 2 col desktop) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        
        <!-- KOLOM KIRI (Card Utama - Lebar 2 kolom di desktop) -->
        <div class="lg:col-span-2">
            <!-- 2. Primary Action Card (Status Absensi) -->
            <div class="relative overflow-hidden rounded-3xl border border-gray-100 bg-white p-6 md:p-10 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] ring-1 ring-gray-900/5 transition-all hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.05)] h-full flex flex-col justify-center">
                <div class="flex flex-col items-center justify-center text-center py-2 h-full">
                    
                    @if($sudahAbsen)
                        <!-- State: Sudah Absen -->
                        <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-green-50 text-green-500 shadow-sm ring-4 ring-green-50/50">
                            <i class="fas fa-check text-3xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Kamu Sudah Absen</h3>
                        <p class="mt-2 text-gray-500 max-w-sm mx-auto">Absensi kamu tercatat pada pukul <strong class="text-gray-800">{{ \Carbon\Carbon::parse($sudahAbsen->jam_masuk)->format('H:i') }} WIB</strong>. Selamat belajar!</p>
                        
                        <div class="mt-8 w-full max-w-xs">
                            <button disabled class="w-full flex items-center justify-center gap-2 rounded-xl bg-gray-50 px-6 py-3.5 text-sm font-semibold text-gray-400 cursor-not-allowed border border-gray-100">
                                <i class="fas fa-qrcode"></i> Scan QR Lagi
                            </button>
                        </div>
                    @else
                        <!-- State: Belum Absen -->
                        <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-blue-600 shadow-sm ring-4 ring-blue-50/50 animate-pulse">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Belum Melakukan Absensi</h3>
                        <p class="mt-2 text-gray-500 max-w-sm mx-auto">Silakan scan QR code yang ditampilkan guru di depan kelas untuk mencatat kehadiran.</p>
                        
                        <div class="mt-8 w-full max-w-xs">
                            <a href="{{ route('siswa.scan_qr') }}" class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 transition-all active:scale-[0.98]">
                                <i class="fas fa-qrcode"></i> Scan QR Code
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (Statistik & Riwayat) -->
        <div class="space-y-6 md:space-y-8 flex flex-col mt-4 lg:mt-0">
            
            <!-- 3. Ringkasan Statistik (Grid Compact) -->
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 px-1">Statistik Semester Ini</h3>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Hadir -->
                    <div class="flex flex-col p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-gray-200 transition">
                        <span class="text-[10px] uppercase font-bold text-gray-400 mb-1">Hadir</span>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalHadir }}</span>
                    </div>
                    <!-- Izin -->
                    <div class="flex flex-col p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-gray-200 transition">
                        <span class="text-[10px] uppercase font-bold text-gray-400 mb-1">Izin</span>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalIzin }}</span>
                    </div>
                    <!-- Sakit -->
                    <div class="flex flex-col p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:border-gray-200 transition">
                        <span class="text-[10px] uppercase font-bold text-gray-400 mb-1">Sakit</span>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalSakit }}</span>
                    </div>
                    <!-- Absen -->
                    <div class="flex flex-col p-4 rounded-2xl bg-white border border-red-100 shadow-sm hover:border-red-200 transition">
                        <span class="text-[10px] uppercase font-bold text-red-300 mb-1">Absen</span>
                        <span class="text-2xl font-bold text-red-500">{{ $totalAbsen }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. Riwayat Singkat (Clean List) -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-4 px-1 mt-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Aktivitas Terakhir</h3>
                    <a href="{{ route('siswa.riwayat') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($riwayatTerakhir as $log)
                        <div class="flex items-center justify-between rounded-xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-md hover:border-gray-200 transition-all cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full 
                                    {{ $log->status == 'hadir' ? 'bg-green-50 text-green-600 ring-1 ring-green-100' : 
                                      ($log->status == 'izin' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : 
                                      ($log->status == 'sakit' ? 'bg-yellow-50 text-yellow-600 ring-1 ring-yellow-100' : 'bg-red-50 text-red-600 ring-1 ring-red-100')) }}">
                                    <i class="fas {{ $log->status == 'hadir' ? 'fa-check' : 'fa-info' }} text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 capitalize truncate">{{ $log->keterangan ?? $log->status }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('dddd, D MMM') }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-xs font-bold text-gray-500 bg-gray-50 px-2.5 py-1 rounded-md">
                                    {{ $log->jam_masuk ? \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 rounded-xl border border-dashed border-gray-200 bg-gray-50/50">
                            <p class="text-sm text-gray-400">Belum ada riwayat absensi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection