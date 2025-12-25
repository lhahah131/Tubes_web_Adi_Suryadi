@extends('layouts.app')

@section('content')
    <style>
        .form-container {
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

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .file-upload-area.dragover {
            border-color: #2563eb;
            background-color: #dbeafe;
        }
    </style>

    <div class="max-w-4xl mx-auto p-4 md:p-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Pengajuan Surat Izin/Sakit</h2>
            <p class="text-gray-600">Ajukan surat izin atau sakit dengan melampirkan bukti pendukung</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-red-800 font-medium mb-2">Terjadi kesalahan:</p>
                    <ul class="list-disc ml-5 text-red-700 text-sm">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Pengajuan -->
        <div class="form-container bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('siswa.pengajuan_izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Jenis Pengajuan -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Jenis Pengajuan <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="jenis" value="sakit" class="hidden peer" required>
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-lg p-4 text-center transition">
                                <i class="fas fa-notes-medical text-3xl text-red-500 mb-2"></i>
                                <p class="font-semibold text-gray-800">Sakit</p>
                            </div>
                        </label>
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="jenis" value="izin" class="hidden peer" required>
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-lg p-4 text-center transition">
                                <i class="fas fa-file-alt text-3xl text-blue-500 mb-2"></i>
                                <p class="font-semibold text-gray-800">Izin</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Mata Pelajaran -->
                <div class="mb-6">
                    <label for="mata_pelajaran" class="block text-sm font-semibold text-gray-700 mb-2">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="mata_pelajaran" 
                        name="mata_pelajaran" 
                        value="{{ old('mata_pelajaran') }}"
                        placeholder="Masukkan nama mata pelajaran"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                        required>
                </div>

                <!-- Tanggal Izin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="tanggal_mulai" 
                            name="tanggal_mulai" 
                            value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                            required>
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="tanggal_selesai" 
                            name="tanggal_selesai" 
                            value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                            required>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan/Alasan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="keterangan" 
                        name="keterangan" 
                        rows="4" 
                        placeholder="Jelaskan alasan izin/sakit Anda..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        required>{{ old('keterangan') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter</p>
                </div>

                <!-- Upload File Bukti -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        File Surat Bukti <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <div class="file-upload-area rounded-lg p-6 text-center" id="fileUploadArea">
                        <input 
                            type="file" 
                            id="file_bukti" 
                            name="file_bukti" 
                            accept="image/*,.pdf"
                            class="hidden">
                        <label for="file_bukti" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                            <p class="text-gray-700 font-medium mb-1">Klik untuk upload atau drag & drop</p>
                            <p class="text-sm text-gray-500">Format: JPG, PNG, PDF (Max 2MB)</p>
                        </label>
                    </div>
                    <div id="filePreview" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file text-blue-500 text-2xl mr-3"></i>
                                <div>
                                    <p id="fileName" class="font-medium text-gray-800"></p>
                                    <p id="fileSize" class="text-sm text-gray-600"></p>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3 flex-shrink-0"></i>
                        <div class="text-yellow-800 text-sm">
                            <strong>Catatan:</strong>
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                <li>Pastikan tanggal yang diisi sesuai dengan periode izin/sakit</li>
                                <li>Upload surat keterangan dokter untuk sakit atau surat izin dari orang tua</li>
                                <li>Pengajuan akan diverifikasi oleh guru</li>
                                <li>Anda akan mendapat notifikasi setelah pengajuan disetujui/ditolak</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg transition font-semibold">
                        <i class="fas fa-paper-plane mr-2"></i>Ajukan Permohonan
                    </button>
                    <a href="{{ route('siswa.riwayat') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition font-semibold text-center">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('file_bukti');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const filePreview = document.getElementById('filePreview');

        // Click to upload
        fileUploadArea.addEventListener('click', () => {
            if (!fileInput.files.length) {
                fileInput.click();
            }
        });

        // Drag and drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });

        function displayFileInfo(file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('⚠️ Ukuran file terlalu besar! Maksimal 2MB');
                fileInput.value = '';
                return;
            }

            // Display file info
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');
            fileUploadArea.style.display = 'none';
        }

        function removeFile() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
            fileUploadArea.style.display = 'block';
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

    </script>
@endsection
