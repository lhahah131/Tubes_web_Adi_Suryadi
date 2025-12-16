<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Sistem Absensi QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8faff 100%);
        }
    </style>
</head>

<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-qrcode text-blue-600 text-2xl mr-3"></i>
                <h1 class="text-2xl font-bold text-gray-800">Absensi QR</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar & Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md min-h-screen">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Menu</h2>
                <nav class="space-y-2">
                    <a href="{{ route('siswa.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-home mr-3"></i>Dashboard
                    </a>
                    <a href="{{ route('siswa.absensi') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.absensi') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-qrcode mr-3"></i>Absensi QR
                    </a>
                    <a href="{{ route('siswa.pengajuan_izin') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.pengajuan_izin') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-file-medical mr-3"></i>Pengajuan Izin
                    </a>
                    <a href="{{ route('siswa.riwayat') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.riwayat') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-history mr-3"></i>Riwayat
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-4xl">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Siswa</h2>

                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-md p-8 text-white mb-8">
                    <h3 class="text-2xl font-bold mb-2">Selamat Datang!</h3>
                    <p class="text-blue-100">Silakan scan QR code untuk melakukan absensi hari ini.</p>
                </div>



                <!-- QR Scanner Section -->
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-qrcode text-6xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Absensi QR Code</h3>
                    <p class="text-gray-600 mb-6">Arahkan kamera ke QR code untuk melakukan absensi</p>
                    <button onclick="window.location.href='{{ route('siswa.scan_qr') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition text-lg font-semibold">
                        <i class="fas fa-camera mr-2"></i>Buka Scanner
                    </button>
                </div>

                <!-- Info -->
                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-lightbulb text-yellow-600 mt-0.5 mr-3 flex-shrink-0"></i>
                        <p class="text-yellow-800 text-sm">
                            <strong>Tips:</strong> Pastikan kondisi pencahayaan cukup untuk pemindaian QR code. Jika scan gagal, coba lagi atau hubungi guru.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>