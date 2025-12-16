@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Welcome Banner with Date -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow-lg p-6 mb-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <p class="opacity-90">Silakan scan QR code untuk melakukan absensi hari ini.</p>
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-lg font-semibold">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-xl"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        
        <!-- Total Hadir -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500 font-medium">Total Hadir</p>
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalHadir }}</h3>
            <p class="text-xs text-green-600 mt-1 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i> Kehadiran
            </p>
        </div>

        <!-- Total Sakit -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500 font-medium">Total Sakit</p>
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-procedures text-yellow-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalSakit }}</h3>
            <p class="text-xs text-gray-400 mt-1">Dengan surat dokter</p>
        </div>

        <!-- Total Izin -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500 font-medium">Total Izin</p>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope-open-text text-blue-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalIzin }}</h3>
            <p class="text-xs text-gray-400 mt-1">Izin disetujui</p>
        </div>

        <!-- Total Absen -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500 font-medium">Total Absen</p>
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalAbsen }}</h3>
            <p class="text-xs text-red-500 mt-1">Tanpa keterangan</p>
        </div>

         <!-- Persentase Kehadiran -->
         <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-500 font-medium">Persentase</p>
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-pie text-indigo-500 text-sm"></i>
                    </div>
                </div>
                <!-- Logic tambahan: Tampilkan 0% jika belum ada data agar tidak error -->
                <h3 class="text-2xl font-bold text-indigo-600">{{ $persentaseKehadiran ?? 0 }}%</h3>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $persentaseKehadiran ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h2 class="text-xl font-bold text-gray-800 mb-4 px-1">Menu Cepat</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('siswa.scan_qr') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center border-l-4 border-l-blue-500">
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition">Scan QR Code</h3>
                <p class="text-sm text-gray-500">Klik disni untuk melakukan absensi</p>
            </div>
            <div class="ml-auto text-gray-300 group-hover:text-blue-500 transition">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>

        <a href="{{ route('siswa.riwayat') }}" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center border-l-4 border-l-purple-500">
            <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition">
                <i class="fas fa-history text-purple-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 group-hover:text-purple-600 transition">Riwayat Absensi</h3>
                <p class="text-sm text-gray-500">Lihat semua catatan kehadiran Anda</p>
            </div>
            <div class="ml-auto text-gray-300 group-hover:text-purple-500 transition">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
    </div>
</div>
@endsection