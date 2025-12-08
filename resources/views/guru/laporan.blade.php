<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Laporan - Sistem Absensi QR</title>
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
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Laporan Absensi</h2>

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Bulan</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option>Desember 2025</option>
                                <option>November 2025</option>
                                <option>Oktober 2025</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Kelas</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option>Semua Kelas</option>
                                <option>X-A</option>
                                <option>X-B</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-gray-700 font-semibold">No</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-semibold">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-semibold">Kelas</th>
                                <th class="px-6 py-3 text-center text-gray-700 font-semibold">Hadir</th>
                                <th class="px-6 py-3 text-center text-gray-700 font-semibold">Absen</th>
                                <th class="px-6 py-3 text-center text-gray-700 font-semibold">Sakit</th>
                                <th class="px-6 py-3 text-center text-gray-700 font-semibold">Izin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-800">1</td>
                                <td class="px-6 py-4 text-gray-800">Budi Santoso</td>
                                <td class="px-6 py-4 text-gray-800">X-A</td>
                                <td class="px-6 py-4 text-center"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">28</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">2</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">0</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">0</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-800">2</td>
                                <td class="px-6 py-4 text-gray-800">Siti Nurhaliza</td>
                                <td class="px-6 py-4 text-gray-800">X-A</td>
                                <td class="px-6 py-4 text-center"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">29</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">1</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">0</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">0</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-800">3</td>
                                <td class="px-6 py-4 text-gray-800">Ahmad Ridho</td>
                                <td class="px-6 py-4 text-gray-800">X-A</td>
                                <td class="px-6 py-4 text-center"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">27</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">1</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">1</span></td>
                                <td class="px-6 py-4 text-center"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">1</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Export Button -->
                <div class="mt-6 flex gap-4">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>