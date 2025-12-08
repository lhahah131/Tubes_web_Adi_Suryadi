<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR Code - Sistem Absensi QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8faff 100%);
        }

        #reader {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .scanner-container {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-animation {
            animation: successPulse 0.6s ease-out;
        }

        @keyframes successPulse {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
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
                        <i class="fas fa-qrcode mr-3"></i>Scan QR
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
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Scan QR Code Absensi</h2>

                <!-- Info Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 text-2xl mt-0.5 mr-4 flex-shrink-0"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Cara Melakukan Absensi:</h3>
                            <ol class="list-decimal ml-5 text-blue-800 space-y-1">
                                <li>Klik tombol "Buka Scanner" di bawah</li>
                                <li>Izinkan akses kamera saat diminta</li>
                                <li>Arahkan kamera ke QR Code yang ditampilkan oleh guru</li>
                                <li>Tunggu hingga QR Code berhasil di-scan</li>
                                <li>Absensi Anda akan tercatat otomatis</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Scanner Section -->
                <div id="scannerSection" class="bg-white rounded-lg shadow-md p-8">
                    <div class="text-center mb-6">
                        <i class="fas fa-camera text-6xl text-blue-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">QR Code Scanner</h3>
                        <p class="text-gray-600">Klik tombol di bawah untuk membuka scanner</p>
                    </div>

                    <button id="startScanBtn" onclick="startScanner()" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-4 rounded-lg transition text-lg font-semibold">
                        <i class="fas fa-camera mr-2"></i>Buka Scanner
                    </button>

                    <!-- Scanner Container (Hidden initially) -->
                    <div id="scannerContainer" class="hidden scanner-container mt-6">
                        <div id="reader" class="w-full"></div>
                        <button id="stopScanBtn" onclick="stopScanner()" class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                            <i class="fas fa-stop mr-2"></i>Tutup Scanner
                        </button>
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-800 text-sm text-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                <strong>Tips:</strong> Pastikan pencahayaan cukup dan QR Code terlihat jelas di kamera
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Success Result (Hidden initially) -->
                <div id="successSection" class="hidden success-animation mt-8 bg-white rounded-lg shadow-md p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Absensi Berhasil!</h3>
                        <p class="text-gray-600">Data absensi Anda telah tercatat</p>
                    </div>

                    <!-- Attendance Details -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-green-900 mb-4 text-center">Detail Absensi</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600 font-semibold">Nama:</p>
                                <p id="resultNama" class="text-gray-800 font-bold">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Kelas:</p>
                                <p id="resultKelas" class="text-gray-800 font-bold">-</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Mata Pelajaran:</p>
                                <p id="resultMapel" class="text-gray-800 font-bold">-</p>
                            </div> 
                            <div>
                                <p class="text-gray-600 font-semibold">Jam Pelajaran:</p>
                                <p id="resultJam" class="text-gray-800 font-bold">-</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Guru:</p>
                                <p id="resultGuru" class="text-gray-800 font-bold">-</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Waktu:</p>
                                <p id="resultWaktu" class="text-gray-800 font-bold">-</p>
                            </div>
                        </div>
                    </div>

                    <button onclick="resetScanner()" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                        <i class="fas fa-redo mr-2"></i>Scan Lagi
                    </button>
                </div>

                <!-- Error Section (Hidden initially) -->
                <div id="errorSection" class="hidden mt-8 bg-white rounded-lg shadow-md p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                            <i class="fas fa-exclamation-circle text-red-500 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Absensi Gagal</h3>
                        <p id="errorMessage" class="text-gray-600">Terjadi kesalahan</p>
                    </div>
                    <button onclick="resetScanner()" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner = null;

        async function startScanner() {
            try {
                document.getElementById('startScanBtn').classList.add('hidden');
                document.getElementById('scannerContainer').classList.remove('hidden');

                html5QrcodeScanner = new Html5Qrcode("reader");

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                await html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanError
                );
            } catch (err) {
                console.error('Error starting scanner:', err);
                alert('⚠️ Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                resetScanner();
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('QR Code detected:', decodedText);
            
            // Stop scanner immediately
            stopScanner();

            try {
                // Parse QR data
                const qrData = JSON.parse(decodedText);

                // Validate QR data
                if (!qrData.guru_id || !qrData.kelas || !qrData.mata_pelajaran) {
                    throw new Error('QR Code tidak valid');
                }

                // Submit attendance
                submitAttendance(qrData);

            } catch (err) {
                console.error('Error parsing QR:', err);
                showError('QR Code tidak valid atau sudah expired');
            }
        }

        function onScanError(errorMessage) {
            // Ignore scan errors (they happen frequently during scanning)
        }

        async function submitAttendance(qrData) {
            try {
                // Show success immediately for demo
                // In production, you would send this to the server first
                
                // Display success
                document.getElementById('scannerSection').classList.add('hidden');
                document.getElementById('resultKelas').textContent = qrData.kelas;
                document.getElementById('resultMapel').textContent = qrData.mata_pelajaran;
                document.getElementById('resultJam').textContent = `Jam ke-${qrData.jam_pelajaran}`;
                document.getElementById('resultGuru').textContent = qrData.guru_name;
                document.getElementById('resultWaktu').textContent = new Date().toLocaleString('id-ID');
                document.getElementById('successSection').classList.remove('hidden');

                // Here you would normally send data to server
                // const response = await fetch('/api/absensi/submit', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify({
                //         qr_data: qrData,
                //         siswa_id: {{ Auth::id() }}
                //     })
                // });

            } catch (err) {
                console.error('Error submitting attendance:', err);
                showError('Gagal menyimpan absensi. Silakan coba lagi.');
            }
        }

        async function stopScanner() {
            if (html5QrcodeScanner) {
                try {
                    await html5QrcodeScanner.stop();
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                } catch (err) {
                    console.error('Error stopping scanner:', err);
                }
            }
            document.getElementById('scannerContainer').classList.add('hidden');
            document.getElementById('startScanBtn').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('scannerSection').classList.add('hidden');
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorSection').classList.remove('hidden');
        }

        function resetScanner() {
            document.getElementById('successSection').classList.add('hidden');
            document.getElementById('errorSection').classList.add('hidden');
            document.getElementById('scannerSection').classList.remove('hidden');
            document.getElementById('scannerContainer').classList.add('hidden');
            document.getElementById('startScanBtn').classList.remove('hidden');
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop();
            }
        });
    </script>
</body>

</html>
