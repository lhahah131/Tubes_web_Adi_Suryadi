@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 md:p-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('guru.laporan') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Laporan
                </a>
                <h2 class="text-3xl font-bold text-gray-800">Detail Absensi</h2>
                <p class="text-gray-600 mt-1">Siswa: <strong>{{ $siswa->user->name }}</strong> ({{ $siswa->kelas->nama_kelas ?? '-' }})</p>
            </div>
            
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Jam</th>
                        <th class="px-6 py-3 text-center text-gray-700 font-semibold">Status</th>
                        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Keterangan</th>
                        <th class="px-6 py-3 text-center text-gray-700 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $absen)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-800">{{ $absen->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClass = match(strtolower($absen->status)) {
                                        'hadir' => 'bg-green-100 text-green-800',
                                        'sakit' => 'bg-yellow-100 text-yellow-800',
                                        'izin' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-red-100 text-red-800'
                                    };
                                @endphp
                                <span class="{{ $statusClass }} px-3 py-1 rounded-full text-sm font-semibold capitalize">
                                    {{ $absen->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm max-w-xs truncate" title="{{ $absen->keterangan }}">
                                {{ $absen->keterangan }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('guru.absensi.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Belum ada riwayat absensi untuk siswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
