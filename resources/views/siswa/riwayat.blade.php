@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-4 md:p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Riwayat Absensi</h2>

        <!-- Attendance History -->
        <div class="space-y-4">
            <!-- Empty State / Data from Database -->
            @forelse($attendances ?? [] as $attendance)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($attendance->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</p>
                            <h3 class="text-lg font-bold text-gray-800">
                                @if($attendance->status === 'hadir')
                                    Jam Masuk: {{ $attendance->jam_masuk }}
                                @elseif($attendance->status === 'sakit')
                                    Keterangan: Sakit
                                @elseif($attendance->status === 'izin')
                                    Keterangan: Izin
                                @else
                                    -
                                @endif
                            </h3>
                        </div>
                        <div class="flex items-center">
                            @if($attendance->status === 'hadir')
                                <i class="fas fa-check-circle text-green-500 text-4xl mr-4"></i>
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">Hadir</span>
                            @elseif($attendance->status === 'sakit')
                                <i class="fas fa-hospital-user text-yellow-500 text-4xl mr-4"></i>
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold">Sakit</span>
                            @elseif($attendance->status === 'izin')
                                <i class="fas fa-hand-paper text-blue-500 text-4xl mr-4"></i>
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">Izin</span>
                            @else
                                <i class="fas fa-times-circle text-red-500 text-4xl mr-4"></i>
                                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-semibold">Absen</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- No Data Yet - Waiting for Mobile Scan -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="mb-6">
                        <i class="fas fa-mobile-alt text-gray-400 text-6xl mb-4"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Riwayat Absensi</h3>
                    <p class="text-gray-600 mb-6">
                        Riwayat absensi akan muncul di sini setelah Anda melakukan scan QR code dari mobile app.
                    </p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                        <p class="text-blue-800 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Cara absensi:</strong> Buka menu <strong>Absensi QR</strong> dan scan QR code yang ditampilkan guru di kelas.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection