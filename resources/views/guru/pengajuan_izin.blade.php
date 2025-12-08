<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Pengajuan Izin - Sistem Absensi QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8faff 100%);
        }

        .status-badge {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
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
                    <a href="{{ route('guru.absensi') }}" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-qrcode mr-3"></i>Generate QR
                    </a>
                    <a href="{{ route('guru.pengajuan_izin') }}" class="block px-4 py-3 rounded-lg bg-blue-500 text-white transition">
                        <i class="fas fa-file-medical mr-3"></i>Pengajuan Izin
                        @if($pending_count > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pending_count }}</span>
                        @endif
                    </a>
                    <a href="{{ route('guru.laporan') }}" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-chart-bar mr-3"></i>Laporan
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Kelola Pengajuan Izin/Sakit</h2>
                        <p class="text-gray-600">Review dan setujui pengajuan izin dari siswa</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="filterStatus('all')" class="filter-btn px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition text-sm">
                            Semua ({{ $pengajuan->count() }})
                        </button>
                        <button onclick="filterStatus('pending')" class="filter-btn px-4 py-2 rounded-lg bg-yellow-100 hover:bg-yellow-200 transition text-sm">
                            Pending ({{ $pending_count }})
                        </button>
                        <button onclick="filterStatus('approved')" class="filter-btn px-4 py-2 rounded-lg bg-green-100 hover:bg-green-200 transition text-sm">
                            Approved
                        </button>
                        <button onclick="filterStatus('rejected')" class="filter-btn px-4 py-2 rounded-lg bg-red-100 hover:bg-red-200 transition text-sm">
                            Rejected
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
                </div>
                @endif

                <!-- Pengajuan List -->
                <div class="space-y-4">
                    @forelse($pengajuan as $item)
                    <div class="bg-white rounded-lg shadow-md p-6 pengajuan-card" data-status="{{ $item->status }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $item->user->name }}</h3>
                                    @if($item->jenis === 'sakit')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                        <i class="fas fa-notes-medical mr-1"></i>Sakit
                                    </span>
                                    @else
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                        <i class="fas fa-file-alt mr-1"></i>Izin
                                    </span>
                                    @endif
                                    
                                    @if($item->status === 'pending')
                                    <span class="status-badge px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                    @elseif($item->status === 'approved')
                                    <span class="status-badge px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Disetujui
                                    </span>
                                    @else
                                    <span class="status-badge px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1"></i>Ditolak
                                    </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $item->tanggal_mulai->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tanggal Selesai</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $item->tanggal_selesai->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Durasi</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $item->tanggal_mulai->diffInDays($item->tanggal_selesai) + 1 }} hari</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tanggal Pengajuan</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $item->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 mb-1">Keterangan</p>
                                    <p class="text-sm text-gray-700">{{ $item->keterangan }}</p>
                                </div>

                                @if($item->file_bukti)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500 mb-2">File Bukti</p>
                                    <a href="{{ asset('storage/' . $item->file_bukti) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                        <i class="fas fa-paperclip mr-2 text-blue-600"></i>
                                        <span class="text-sm text-blue-700 font-medium">Lihat File Bukti</span>
                                    </a>
                                </div>
                                @endif

                                @if($item->status !== 'pending' && $item->approver)
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ $item->status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh: <strong>{{ $item->approver->name }}</strong>
                                        pada {{ $item->approved_at->format('d M Y H:i') }}
                                    </p>
                                    @if($item->catatan_guru)
                                    <p class="text-sm text-gray-700 mt-2"><strong>Catatan:</strong> {{ $item->catatan_guru }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>

                            @if($item->status === 'pending')
                            <div class="ml-4 flex gap-2">
                                <button onclick="openApprovalModal({{ $item->id }}, 'approve')" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition font-semibold">
                                    <i class="fas fa-check mr-2"></i>Setujui
                                </button>
                                <button onclick="openApprovalModal({{ $item->id }}, 'reject')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-semibold">
                                    <i class="fas fa-times mr-2"></i>Tolak
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Ada Pengajuan</h3>
                        <p class="text-gray-600">Belum ada pengajuan izin/sakit dari siswa</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
            <h3 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-4"></h3>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="catatan_guru" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <textarea id="catatan_guru" name="catatan_guru" rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="submit" id="confirmBtn" class="flex-1 px-6 py-3 rounded-lg text-white font-semibold transition">
                        Konfirmasi
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 px-6 py-3 rounded-lg text-white font-semibold transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterStatus(status) {
            const cards = document.querySelectorAll('.pengajuan-card');
            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function openApprovalModal(id, action) {
            const modal = document.getElementById('approvalModal');
            const modalTitle = document.getElementById('modalTitle');
            const form = document.getElementById('approvalForm');
            const confirmBtn = document.getElementById('confirmBtn');

            if (action === 'approve') {
                modalTitle.textContent = 'Setujui Pengajuan';
                confirmBtn.className = 'flex-1 bg-green-500 hover:bg-green-600 px-6 py-3 rounded-lg text-white font-semibold transition';
                confirmBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Setujui';
                form.action = `/guru/pengajuan-izin/${id}/approve`;
            } else {
                modalTitle.textContent = 'Tolak Pengajuan';
                confirmBtn.className = 'flex-1 bg-red-500 hover:bg-red-600 px-6 py-3 rounded-lg text-white font-semibold transition';
                confirmBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Tolak';
                form.action = `/guru/pengajuan-izin/${id}/reject`;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('approvalModal').classList.add('hidden');
            document.getElementById('catatan_guru').value = '';
        }

        // Close modal on outside click
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>

</html>
