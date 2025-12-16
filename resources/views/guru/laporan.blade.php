@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 md:p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Laporan Absensi</h2>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form action="{{ route('guru.laporan') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Bulan</label>
                        <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="">Semua Bulan</option>
                            @for($i = 0; $i < 3; $i++)
                                @php 
                                    $date = \Carbon\Carbon::now()->subMonths($i); 
                                    $val = $date->format('Y-m');
                                    $label = $date->isoFormat('MMMM YYYY');
                                @endphp
                                <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Kelas</label>
                        <select name="kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="all">Semua Kelas</option>
                            @foreach($allKelas ?? [] as $kel)
                                <option value="{{ $kel->id }}" {{ request('kelas_id') == $kel->id ? 'selected' : '' }}>
                                    {{ $kel->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        @if(request('kelas_id') || request('bulan'))
                            <a href="{{ route('guru.laporan') }}" class="w-auto bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition text-center" title="Reset Filter">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
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
                        <th class="px-6 py-3 text-center text-gray-700 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students ?? [] as $index => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-800">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $student->kelas }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">{{ $student->hadir ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">{{ $student->absen ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">{{ $student->sakit ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $student->izin ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('guru.laporan.detail', $student->id) }}" class="text-blue-500 hover:text-blue-700 font-medium p-2 rounded-lg hover:bg-blue-50 transition" title="Lihat Detail">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    <form action="{{ route('guru.absensi.clear', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENGHAPUS SEMUA riwayat absensi siswa ini? Data tidak dapat dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium p-2 rounded-lg hover:bg-red-50 transition" title="Hapus Semua Riwayat">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chart-line text-gray-400 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Data Laporan</h3>
                                    <p class="text-gray-600 mb-4">
                                        Data laporan akan muncul setelah siswa melakukan absensi via scan QR dari mobile app.
                                    </p>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md">
                                        <p class="text-blue-800 text-sm">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Info:</strong> Generate QR code di menu Absensi agar siswa bisa scan dan data mulai tercatat.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
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
@endsection