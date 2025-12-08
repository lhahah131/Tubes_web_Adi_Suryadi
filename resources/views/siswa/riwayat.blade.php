<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - Sistem Absensi QR</title>
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
                    <a href="{{ route('siswa.absensi') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.absensi') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-qrcode mr-3"></i>Absensi QR
                    </a>
                    <a href="{{ route('siswa.riwayat') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.riwayat') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                        <i class="fas fa-history mr-3"></i>Riwayat
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-5xl">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Riwayat Absensi</h2>

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Dari Tanggal</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Sampai Tanggal</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="space-y-4">
                    <!-- Today -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Senin, 8 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">Jam Masuk: 07:15</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-4xl mr-4"></i>
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">Hadir</span>
                            </div>
                        </div>
                    </div>

                    <!-- Yesterday -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Minggu, 7 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">Libur</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-times text-gray-500 text-4xl mr-4"></i>
                                <span class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg font-semibold">Libur</span>
                            </div>
                        </div>
                    </div>

                    <!-- Saturday -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Sabtu, 6 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">Jam Masuk: 07:10</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-4xl mr-4"></i>
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">Hadir</span>
                            </div>
                        </div>
                    </div>

                    <!-- Absent Record -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Jumat, 5 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">-</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-red-500 text-4xl mr-4"></i>
                                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-semibold">Absen</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sick Record -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Kamis, 4 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">Keterangan: Sakit</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hospital-user text-yellow-500 text-4xl mr-4"></i>
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold">Sakit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Record -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Rabu, 3 Desember 2025</p>
                                <h3 class="text-lg font-bold text-gray-800">Keterangan: Izin</h3>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hand-paper text-blue-500 text-4xl mr-4"></i>
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">Izin</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    <button class="px-4 py-2 mx-1 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">← Sebelumnya</button>
                    <button class="px-4 py-2 mx-1 bg-blue-600 text-white rounded-lg">1</button>
                    <button class="px-4 py-2 mx-1 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">2</button>
                    <button class="px-4 py-2 mx-1 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Berikutnya →</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>