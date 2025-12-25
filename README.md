# Aplikasi Absensi Sekolah Berbasis Web (Laravel)

Aplikasi ini ditujukan untuk mempermudah proses absensi siswa dan monitoring kehadiran oleh guru menggunakan teknologi **QR Code**.

## 📂 Struktur Aplikasi (Simplified)

Berikut adalah peta struktur folder utama untuk memahami alur aplikasi (MVC Pattern):

```text
Tubes_Web_Adi_Suryadi/
│
├── app/                        # 🧠 OTAK APLIKASI (Backend Logic)
│   ├── Http/
│   │   └── Controllers/        # Pengendali Logika Bisnis
│   │       ├── AuthController.php      # Login, Register, & Logout
│   │       ├── GuruController.php      # Fitur Guru (Dashboard, Laporan)
│   │       └── SiswaController.php     # Fitur Siswa (Scan QR, Izin)
│   │
│   └── Models/                 # 💾 DATA DATABASE
│       ├── User.php            # Akun Pengguna
│       ├── Siswa.php           # Data Profil Siswa
│       ├── Absensi.php         # Data Kehadiran
│       └── Guru.php            # Data Profil Guru
│
├── resources/                  # 🎨 TAMPILAN (Frontend UI)
│   └── views/                  # Halaman HTML (Blade)
│       ├── auth/               # Halaman Masuk
│       │   └── login.blade.php
│       │
│       ├── guru/               # Area Khusus Guru
│       │   ├── dashboard.blade.php
│       │   ├── generate_qr.blade.php   # Pembuat QR Code
│       │   └── laporan.blade.php       # Rekap Absen
│       │
│       └── siswa/              # Area Khusus Siswa
│           ├── dashboard.blade.php
│           ├── scan_qr.blade.php       # Kamera Scanner
│           └── riwayat.blade.php       # History Kehadiran
│
├── routes/                     # 🛣️ JALUR URL
│   └── web.php                 # Daftar Rute Website
│
└── public/                     # 🌐 ASET PUBLIK
    ├── images/                 # Logo & ikon
    └── js/html5-qrcode.min.js  # Library Scanner Kamera
```

## 🚀 Fitur Utama

1.  **Multi-User Login**: Membedakan hak akses antara **Guru** dan **Siswa**.
2.  **QR Code Generator (Guru)**: Membuat kode QR dinamis setiap hari untuk absensi.
3.  **QR Code Scanner (Siswa)**: Siswa melakukan absen dengan memindai QR Code lewat kamera HP/Laptop.
4.  **Real-time Dashboard**: Menampilkan statistik kehadiran (Hadir, Sakit, Izin, Alpha).
5.  **Laporan & Recap**: Guru dapat melihat rekapitulasi kehadiran seluruh siswa.

## 🛠️ Teknologi yang Digunakan
-   **Framework**: Laravel 11
-   **Database**: MySQL
-   **Frontend**: Tailwind CSS & Blade Template
-   **Library**: HTML5-QRCode Scanner
