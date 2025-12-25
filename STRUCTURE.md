# Struktur Folder Project Tubes

Berikut adalah struktur direktori utama dari aplikasi Absensi Sekolah berbasis Web ini. Struktur ini disederhanakan untuk memudahkan pemahaman alur aplikasi MVC (Model-View-Controller).

```text
Tubes_Web_Adi_Suryadi/
│
├── app/                        # OTAK APLIKASI (Backend Logic)
│   ├── Http/
│   │   └── Controllers/        # Pengendali Logika Bisnis
│   │       ├── AuthController.php      # Mengatur Login/Logout & Role User
│   │       ├── GuruController.php      # Mengatur fitur Guru (Dashboard, Laporan, QR)
│   │       └── SiswaController.php     # Mengatur fitur Siswa (Scan, Absen, Izin)
│   │
│   └── Models/                 # DATA DATABASE (Representasi Tabel)
│       ├── User.php            # Data Akun Login
│       ├── Siswa.php           # Profil Siswa
│       ├── Guru.php            # Profil Guru
│       └── Absensi.php         # Data Rekaman Kehadiran
│
├── resources/                  # TAMPILAN (Frontend UI)
│   ├── css/
│   │   └── app.css             # Konfigurasi Tailwind CSS
│   │
│   └── views/                  # Halaman HTML (Blade Template)
│       ├── auth/
│       │   └── login.blade.php # Halaman Login
│       │
│       ├── guru/               # Halaman Khusus GURU
│       │   ├── dashboard.blade.php
│       │   ├── generate_qr.blade.php
│       │   └── laporan.blade.php
│       │
│       └── siswa/              # Halaman Khusus SISWA
│           ├── dashboard.blade.php
│           ├── scan_qr.blade.php
│           └── riwayat.blade.php
│
├── routes/                     # JALUR URL
│   └── web.php                 # Daftar Rute/Alamat Aplikasi
│
├── public/                     # ASET PUBLIK (Diakses Browser Langsung)
│   ├── images/                 # Folder Gambar & Logo
│   └── js/
│       └── html5-qrcode.min.js # Library Kamera Scanner
│
├── database/                   # DATABASE SETUP
│   └── migrations/             # Skema Tabel Database (Create Tables)
│
├── .env                        # KONFIGURASI (Environment Variables)
└── composer.json               # LIST LIBRARY (Dependensi Project)
```

## Penjelasan Singkat Alur MVC:

1. **User** mengakses URL -> masuk ke **Routes (`web.php`)**.
2. **Routes** mengarahkan ke **Controller** yang sesuai (misal: `SiswaController`).
3. **Controller** meminta data ke **Model** (misal: `Absensi`).
4. **Model** mengambil data dari **Database**.
5. **Controller** mengirim data tersebut ke **View** (misal: `dashboard.blade.php`).
6. **View** menampilkan halaman HTML ke browser **User**.
