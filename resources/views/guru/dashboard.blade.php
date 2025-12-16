@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 md:p-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-blue-100 text-lg">Kelola absensi siswa dengan mudah dan cepat melalui dashboard ini.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 right-20 -mb-10 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        </div>

        <!-- Quick Stats / Shortcuts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Card 1: Absensi -->
            <a href="{{ route('guru.absensi') }}" class="group bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-xl transition transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fas fa-qrcode text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Absensi</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Kelola Absensi QR</h3>
                <p class="text-gray-500 text-sm">Buat QR Code baru untuk sesi pelajaran hari ini.</p>
            </a>

            <!-- Card 2: Pengajuan Izin (Placeholder link, adjust if needed) -->
            <a href="{{ route('guru.pengajuan_izin') }}" class="group bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-xl transition transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-100 p-3 rounded-lg text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition">
                        <i class="fas fa-envelope-open-text text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perizinan</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Cek Pengajuan Izin</h3>
                <p class="text-gray-500 text-sm">Tinjau surat sakit atau izin dari siswa.</p>
            </a>

            <!-- Card 3: Laporan -->
            <a href="{{ route('guru.laporan') }}" class="group bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-xl transition transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Laporan</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Laporan Kehadiran</h3>
                <p class="text-gray-500 text-sm">Lihat rekapitulasi kehadiran siswa per kelas.</p>
            </a>

        </div>

        <!-- Recent Activity / Guidelines Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Guidelines -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <i class="fas fa-info-circle text-gray-400 text-xl mr-3"></i>
                    <h3 class="text-lg font-bold text-gray-800">Panduan Singkat</h3>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mt-0.5 mr-3">1</span>
                        <p class="text-gray-600 text-sm">Masuk ke menu <span class="font-semibold text-gray-800">Absensi</span> untuk membuat QR Code unik setiap awal pelajaran.</p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mt-0.5 mr-3">2</span>
                        <p class="text-gray-600 text-sm">Tampilkan QR Code di layar proyektor agar seluruh siswa dapat melakukan scan.</p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mt-0.5 mr-3">3</span>
                        <p class="text-gray-600 text-sm">Cek menu <span class="font-semibold text-gray-800">Pengajuan Izin</span> jika ada siswa yang tidak hadir karena sakit atau izin.</p>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mt-0.5 mr-3">4</span>
                        <p class="text-gray-600 text-sm">Gunakan menu <span class="font-semibold text-gray-800">Laporan</span> untuk melihat rekap kehadiran bulanan atau semester.</p>
                    </li>
                </ul>
            </div>

            <!-- Calendar / Date Widget -->
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center text-center">
                <div class="mb-2 text-gray-500 font-medium text-lg">{{ \Carbon\Carbon::now()->isoFormat('dddd') }}</div>
                <div class="text-6xl font-extrabold text-blue-600 mb-2">{{ \Carbon\Carbon::now()->format('d') }}</div>
                <div class="text-xl text-gray-800 font-semibold mb-6">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</div>
                
                <div class="w-full bg-blue-50 rounded-lg p-4">
                    <p class="text-blue-800 text-sm font-medium">
                        <i class="far fa-clock mr-2"></i>
                        Waktu Server: {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>

    </div>
@endsection