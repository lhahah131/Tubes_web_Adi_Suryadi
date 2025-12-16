@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 md:p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Guru</h2>

        <!-- Main Section -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-qrcode text-6xl text-gray-300 mb-4 inline-block"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Fitur Absensi QR</h3>
            <p class="text-gray-600 mb-6">Gunakan menu di samping untuk membuka absensi atau melihat laporan</p>
            <button onclick="window.location.href='{{ route('guru.generate_qr') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                <i class="fas fa-qrcode mr-2"></i>Generate QR Code
            </button>
        </div>
    </div>
@endsection