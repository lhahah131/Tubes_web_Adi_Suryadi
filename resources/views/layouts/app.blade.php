<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi') }}</title>
    
    <!-- CDN Tailwind & FontAwesome for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8faff 100%);
        }
    </style>
</head>
<body class="min-h-screen text-gray-800 font-sans antialiased">

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <!-- Logo / Brand (Clickable to Main Menu) -->
            <a href="{{ Auth::check() && Auth::user()->role == 'guru' ? route('guru.dashboard') : route('siswa.dashboard') }}" class="flex items-center hover:opacity-80 transition group">
                <i class="fas fa-qrcode text-blue-600 text-2xl mr-3 group-hover:scale-110 transition"></i>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight group-hover:text-blue-600 transition">Absensi QR</h1>
            </a>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="text-gray-600 hover:text-blue-600 focus:outline-none p-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Desktop User Menu -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <div class="flex items-center text-sm font-medium text-gray-700 mr-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2 text-blue-600">
                            <i class="fas fa-user"></i>
                        </div>
                        {{ Auth::user()->name }}
                        @if(Auth::user()->role == 'siswa' && Auth::user()->siswa && Auth::user()->siswa->kelas)
                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ Auth::user()->siswa->kelas->nama_kelas }}</span>
                        @endif
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition shadow-sm flex items-center text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
                @endauth
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity opacity-0 backdrop-blur-sm md:hidden"></div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu" class="fixed top-0 left-0 w-[280px] h-full bg-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-out md:hidden flex flex-col">
            <!-- Header Drawer -->
            <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-center">
                     <i class="fas fa-qrcode text-blue-600 text-xl mr-3"></i>
                     <span class="font-bold text-lg text-gray-800 tracking-tight">Absensi QR</span>
                </div>
                <button id="close-menu-btn" class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm text-gray-400 hover:text-red-500 hover:bg-red-50 transition border border-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Menu Items -->
            <div class="p-4 space-y-2 flex-1 overflow-y-auto">
                @if(Auth::check() && Auth::user()->role === 'guru')
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 px-2 mt-2">Menu Guru</div>
                    <a href="{{ route('guru.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('guru.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-home w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('guru.absensi') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('guru.absensi') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-clipboard-list w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Absensi</span>
                    </a>
                    <a href="{{ route('guru.pengajuan_izin') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('guru.pengajuan_izin') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-envelope-open-text w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Pengajuan Izin</span>
                    </a>
                    <a href="{{ route('guru.laporan') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('guru.laporan') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-file-alt w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Laporan</span>
                    </a>
                @elseif(Auth::check() && Auth::user()->role === 'siswa')
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 px-2 mt-2">Menu Siswa</div>
                    <a href="{{ route('siswa.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('siswa.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-home w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('siswa.scan_qr') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('siswa.scan_qr') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                        <i class="fas fa-qrcode w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                        <span class="font-medium">Scan QR</span>
                    </a>
                    <a href="{{ route('siswa.riwayat') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('siswa.riwayat') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                         <i class="fas fa-history w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                         <span class="font-medium">Riwayat</span>
                    </a>
                    <a href="{{ route('siswa.pengajuan_izin') }}" class="flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('siswa.pengajuan_izin') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }} transition-all group">
                         <i class="fas fa-envelope-open-text w-6 text-center mr-3 group-hover:scale-110 transition"></i>
                         <span class="font-medium">Izin</span>
                    </a>
                @endif
            </div>

            <!-- Footer Drawer -->
             <div class="p-4 border-t border-gray-100 bg-gray-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 hover:shadow-md transition font-medium">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
            </div>
        </div>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const body = document.body;

        function openMenu() {
            mobileMenuOverlay.classList.remove('hidden');
            // Small delay to allow display:block to apply before opacity transition
            setTimeout(() => {
                mobileMenuOverlay.classList.remove('opacity-0');
                mobileMenu.classList.remove('-translate-x-full');
            }, 10);
            body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeMenu() {
            mobileMenu.classList.add('-translate-x-full');
            mobileMenuOverlay.classList.add('opacity-0');
            
            // Wait for transition to finish before hiding
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
                body.style.overflow = ''; // Restore scrolling
            }, 300);
        }

        mobileMenuBtn.addEventListener('click', openMenu);
        closeMenuBtn.addEventListener('click', closeMenu);
        mobileMenuOverlay.addEventListener('click', closeMenu);
    </script>

    <!-- Main Content Layout -->
    <div class="flex min-h-[calc(100vh-73px)]">
        <!-- Sidebar Navigation (Optional, consistent with Riwayat page) -->
        <aside class="w-64 bg-white shadow-lg hidden md:block relative z-40">
            <div class="p-6">
                <h2 class="text-xs uppercase text-gray-400 font-bold tracking-wider mb-4">Menu Utama</h2>
                <nav class="space-y-2">
                    @if(Auth::check() && Auth::user()->role === 'guru')
                        <a href="{{ route('guru.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.dashboard') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-home w-6 text-center mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('guru.absensi') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.absensi') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-clipboard-list w-6 text-center mr-2"></i>Absensi
                        </a>
                        <a href="{{ route('guru.pengajuan_izin') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.pengajuan_izin') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-envelope-open-text w-6 text-center mr-2"></i>Pengajuan Izin
                        </a>
                        <a href="{{ route('guru.laporan') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('guru.laporan') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-file-alt w-6 text-center mr-2"></i>Laporan
                        </a>
                    @elseif(Auth::check() && Auth::user()->role === 'siswa')
                         <!-- Dashboard -->
                        <a href="{{ route('siswa.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.dashboard') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-home w-6 text-center mr-2"></i>Dashboard
                        </a>
                        
                        <!-- Absensi -->
                        <a href="{{ route('siswa.scan_qr') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.scan_qr') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                            <i class="fas fa-qrcode w-6 text-center mr-2"></i>Scan QR
                        </a>

                        <!-- Riwayat -->
                        <a href="{{ route('siswa.riwayat') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.riwayat') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                             <i class="fas fa-history w-6 text-center mr-2"></i>Riwayat
                        </a>
                        
                        <!-- Izin -->
                        <a href="{{ route('siswa.pengajuan_izin') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('siswa.pengajuan_izin') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }} transition flex items-center">
                             <i class="fas fa-envelope-open-text w-6 text-center mr-2"></i>Izin
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Dynamic Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50">
            @yield('content')
        </main>
    </div>

</body>
</html>
