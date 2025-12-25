@echo off
set PHP_BIN=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe

if not exist "%PHP_BIN%" (
    echo [ERROR] File PHP Laragon tidak ditemukan di lokasi standar (C:\laragon\...).
    echo Mohon abaikan file ini dan jalankan perintah manual di Terminal Laragon.
    pause
    exit /b
)

echo [INFO] Menggunakan PHP dari Laragon untuk memperbaiki database...
"%PHP_BIN%" artisan config:clear
"%PHP_BIN%" artisan migrate:fresh --seed

if %errorlevel% equ 0 (
    echo.
    echo [SUKSES] Database berhasil di-reset dan diisi data!
    echo Silakan buka phpMyAdmin dan cek tabel 'users'.
) else (
    echo.
    echo [GAGAL] Terjadi kesalahan. Silakan cek pesan error di atas.
)
pause
