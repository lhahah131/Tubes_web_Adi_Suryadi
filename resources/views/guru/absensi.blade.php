<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Sistem Absensi QR</title>
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
                    <a href="{{ route('guru.absensi') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.absensi') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-clipboard-list mr-3"></i>Absensi
                    </a>
                    <a href="{{ route('guru.laporan') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.laporan') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-chart-bar mr-3"></i>Laporan
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-5xl">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Guru</h2>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Siswa</p>
                                <p class="text-3xl font-bold text-blue-600">42</p>
                            </div>
                            <i class="fas fa-users text-blue-400 text-3xl opacity-30"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Hadir Hari Ini</p>
                                <p class="text-3xl font-bold text-green-600">38</p>
                            </div>
                            <i class="fas fa-check-circle text-green-400 text-3xl opacity-30"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Absen</p>
                                <p class="text-3xl font-bold text-red-600">4</p>
                            </div>
                            <i class="fas fa-times-circle text-red-400 text-3xl opacity-30"></i>
                        </div>
                    </div>
                </div>

                <!-- Main Section -->
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-qrcode text-6xl text-gray-300 mb-4 inline-block"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Fitur Absensi QR</h3>
                    <p class="text-gray-600 mb-6">Gunakan menu di samping untuk membuka absensi atau melihat laporan</p>
                    <button onclick="window.location.href='{{ route('guru.generate_qr') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-qrcode mr-2"></i>Generate QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>