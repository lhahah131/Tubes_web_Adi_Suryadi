@extends('layouts.app')

@section('content')
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
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

    <div class="max-w-4xl mx-auto p-4 md:p-8">
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
                
                <!-- NEW: Reset Button for troubleshooting -->
                <button onclick="window.location.reload()" class="mt-2 w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    <i class="fas fa-sync mr-2"></i>Reset Kamera
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

    <script>
        // Make variables global and accessible
        window.isScanning = false;
        window.html5QrcodeScanner = null;

        // Export startScanner to window
        window.startScanner = async function() {
            console.log('Starting scanner...');
            
            // Prevent multiple initializations
            if (window.isScanning) return;

            try {
                // UI Clean up
                document.getElementById('startScanBtn').classList.add('hidden');
                document.getElementById('scannerContainer').classList.remove('hidden');
                document.getElementById('successSection').classList.add('hidden');
                document.getElementById('errorSection').classList.add('hidden');

                // Clear existing instance if any
                if (window.html5QrcodeScanner) {
                    try {
                        await window.html5QrcodeScanner.clear();
                    } catch (e) {
                        console.log('Cleanup error', e);
                    }
                }

                // New Instance
                window.html5QrcodeScanner = new Html5Qrcode("reader");

                const config = {
                    fps: 15,
                    qrbox: 280,
                    aspectRatio: 1.0,
                    verbose: false
                };

                // Start Camera
                await window.html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanError
                );
                
                window.isScanning = true;

            } catch (err) {
                console.error('Error starting scanner:', err);
                window.isScanning = false;
                
                // Show friendly error
                alert('⚠️ Gagal mengakses kamera. \n1. Pastikan izin kamera diaktifkan di browser.\n2. Pastikan Anda menggunakan HTTPS (Cloudflared).');
                
                // Reset UI
                resetScanner();
            }
        }

        async function onScanSuccess(decodedText) {
            if (window.isScanning === false) return; // Prevent double trigger
            
            // Stop scanning logic
            window.isScanning = false;
            await window.stopScanner();
            
            console.log('Scanned:', decodedText);
            
            // Submit immediately without extra parsing logic
            // Let backend handle the JSON/String parsing via submitAttendance
            submitAttendance(decodedText);
        }

        function onScanError(errorMessage) {
            // Silence is golden
        }


       
        async function submitAttendance(decodedText) {
            try {
                // Konversi object ke JSON string jika perlu, atau kirim apa adanya
                const payload = typeof decodedText === 'object' ? JSON.stringify(decodedText) : decodedText;
                
                console.log("Submitting:", payload);

                const response = await fetch("{{ route('siswa.scan_qr.submit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qr_code: payload
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Gagal menyimpan absensi');
                }

                // Tampilkan sukses
                document.getElementById('scannerSection').classList.add('hidden');
                
                // Isi data detail jika ada dari backend
                if (result.data) {
                    document.getElementById('resultKelas').textContent = result.data.kelas || '-';
                    document.getElementById('resultJam').textContent = result.data.jam_masuk || '-';
                    document.getElementById('resultWaktu').textContent = new Date().toLocaleString('id-ID');
                }
                
                document.getElementById('successSection').classList.remove('hidden');

            } catch (err) {
                console.error(err);
                showError(err.message);
            }
        }



        // Export stopScanner
        window.stopScanner = async function() {
            if (window.html5QrcodeScanner) {
                try {
                    // Try stop if running
                    try {
                        await window.html5QrcodeScanner.stop();
                    } catch (ignore) {}
                    
                    window.html5QrcodeScanner.clear();
                } catch (err) {
                    console.error('Error stopping scanner:', err);
                }
            }
            window.isScanning = false;
            
            // UI Reset
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
@endsection
