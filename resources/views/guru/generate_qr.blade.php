@extends('layouts.app')

@section('content')
    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        .qr-container {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        #qrcode {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>

    <div class="max-w-5xl mx-auto p-4 md:p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Generate QR Code Absensi</h2>

        <!-- Session Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Informasi Sesi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select id="kelasSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="X-A">X-A</option>
                        <option value="X-B">X-B</option>
                        <option value="XI-A">XI-A</option>
                        <option value="XI-B">XI-B</option>
                        <option value="XII-A">XII-A</option>
                        <option value="XII-B">XII-B</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                    <input type="text" id="mataPelajaran" placeholder="Contoh: Matematika" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="tanggal" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Pelajaran</label>
                    <select id="jamPelajaran" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="1">Jam ke-1 (07:00 - 07:45)</option>
                        <option value="2">Jam ke-2 (07:45 - 08:30)</option>
                        <option value="3">Jam ke-3 (08:30 - 09:15)</option>
                        <option value="4">Jam ke-4 (09:15 - 10:00)</option>
                        <option value="5">Jam ke-5 (10:15 - 11:00)</option>
                        <option value="6">Jam ke-6 (11:00 - 11:45)</option>
                        <option value="7">Jam ke-7 (12:30 - 13:15)</option>
                        <option value="8">Jam ke-8 (13:15 - 14:00)</option>
                    </select>
                </div>
            </div>
            <button onclick="generateQR()" class="mt-6 w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg transition font-semibold">
                <i class="fas fa-qrcode mr-2"></i>Generate QR Code
            </button>
        </div>

        <!-- QR Code Display -->
        <div id="qrSection" class="hidden qr-container">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">QR Code Absensi</h3>
                
                <!-- Session Details -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 font-semibold">Kelas:</p>
                            <p id="displayKelas" class="text-gray-800 font-bold">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">Mata Pelajaran:</p>
                            <p id="displayMapel" class="text-gray-800 font-bold">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">Tanggal:</p>
                            <p id="displayTanggal" class="text-gray-800 font-bold">-</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">Jam Pelajaran:</p>
                            <p id="displayJam" class="text-gray-800 font-bold">-</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-center">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full pulse"></div>
                            <span class="text-green-700 font-semibold">Sesi Aktif</span>
                        </div>
                        <span class="mx-3 text-gray-400">|</span>
                        <span id="countdown" class="text-gray-600 font-mono">--:--</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="text-center mb-6">
                    <div id="qrcode" class="inline-block"></div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button onclick="downloadQR()" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                        <i class="fas fa-download mr-2"></i>Download QR
                    </button>
                    <button onclick="resetQR()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                        <i class="fas fa-redo mr-2"></i>Generate Baru
                    </button>
                </div>
            </div>

            <!-- Info -->
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3 flex-shrink-0"></i>
                    <div class="text-yellow-800 text-sm">
                        <strong>Instruksi:</strong>
                        <ul class="list-disc ml-5 mt-2 space-y-1">
                            <li>Tampilkan QR Code ini kepada siswa untuk di-scan</li>
                            <li>QR Code berlaku selama sesi absensi berlangsung</li>
                            <li>Download QR Code jika diperlukan untuk ditampilkan di proyektor</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let qrCodeInstance = null;
        let countdownInterval = null;

        async function generateQR() {
            const kelas = document.getElementById('kelasSelect').value;
            const mataPelajaran = document.getElementById('mataPelajaran').value;
            const tanggal = document.getElementById('tanggal').value;
            const jamPelajaran = document.getElementById('jamPelajaran').value;
            const jamText = document.getElementById('jamPelajaran').options[document.getElementById('jamPelajaran').selectedIndex].text;

            // Validation
            if (!mataPelajaran) {
                alert('⚠️ Mohon isi mata pelajaran terlebih dahulu!');
                return;
            }

            // Show Loading
            const btn = document.querySelector('button[onclick="generateQR()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
            btn.disabled = true;

            try {
                // 1. Request Valid Token from Backend
                const response = await fetch("{{ route('guru.generate_qr.post') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        kelas: kelas,
                        mata_pelajaran: mataPelajaran,
                        tanggal: tanggal,
                        jam_pelajaran: jamPelajaran
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Gagal membuat QR Code');
                }

                // 2. Create Optimized QR data using Valid Token
                // Note: result.qr_code is the VALID TOKEN saved in DB
                const qrData = {
                    g: {{ Auth::id() }},         // g = guru_id
                    k: kelas,                    // k = kelas
                    m: mataPelajaran,            // m = mata_pelajaran
                    j: jamPelajaran,             // j = jam_pelajaran
                    t: result.qr_code            // t = TOKEN VALID DARI DB ✅
                };

                const qrString = JSON.stringify(qrData);

                // Clear previous QR
                const qrContainer = document.getElementById('qrcode');
                qrContainer.innerHTML = '';

                // Generate new QR
                qrCodeInstance = new QRCode(qrContainer, {
                    text: qrString,
                    width: 300,
                    height: 300,
                    colorDark: '#1e40af',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.L
                });

                // Update display
                document.getElementById('displayKelas').textContent = kelas;
                document.getElementById('displayMapel').textContent = mataPelajaran;
                document.getElementById('displayTanggal').textContent = formatDate(tanggal);
                document.getElementById('displayJam').textContent = jamText;

                // Show QR section
                document.getElementById('qrSection').classList.remove('hidden');

                // Start countdown (15 min - sesuai validitas DB)
                startCountdown(15 * 60);

                // Scroll to QR
                document.getElementById('qrSection').scrollIntoView({ behavior: 'smooth' });

            } catch (err) {
                console.error('Error:', err);
                alert('Gagal generate QR: ' + err.message);
            } finally {
                // Reset Button
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function startCountdown(seconds) {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            let remaining = seconds;
            const countdownEl = document.getElementById('countdown');

            countdownInterval = setInterval(() => {
                const minutes = Math.floor(remaining / 60);
                const secs = remaining % 60;
                countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    countdownEl.textContent = 'Expired';
                }

                remaining--;
            }, 1000);
        }

        function downloadQR() {
            const canvas = document.querySelector('#qrcode canvas');
            if (canvas) {
                const url = canvas.toDataURL('image/png');
                const a = document.createElement('a');
                a.href = url;
                a.download = `QR_Absensi_${document.getElementById('displayKelas').textContent}_${Date.now()}.png`;
                a.click();
            }
        }

        function resetQR() {
            if (confirm('Generate QR Code baru? Sesi sebelumnya akan berakhir.')) {
                document.getElementById('qrSection').classList.add('hidden');
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                document.querySelector('html').scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Set default date to today
        document.getElementById('tanggal').valueAsDate = new Date();
    </script>
@endsection
