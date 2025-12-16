@echo off
color 0B
title ABSENSI QR - MOBILE (NGROK VERSION)

echo ========================================================
echo   SETUP ABSENSI QR UNTUK MOBILE (MENGGUNAKAN NGROK)
echo ========================================================
echo.

REM 1. Cek apakah ngrok.exe ada di folder ini
if not exist "ngrok.exe" (
    echo [ERROR] FILE NGROK.EXE TIDAK DITEMUKAN!
    echo.
    echo Harap ikuti langkah ini:
    echo 1. Download Ngrok di: https://ngrok.com/download
    echo 2. Pilih "Download for Windows"
    echo 3. Buka file zip yang didownload
    echo 4. COPY file "ngrok.exe" ke dalam folder ini:
    echo    %CD%
    echo.
    echo Jika sudah, jalankan file ini lagi.
    echo.
    pause
    exit
)

echo [1/2] Menjalankan Laravel Server...
start "Laravel Server" cmd /k "php artisan serve"

echo Menunggu server siap...
timeout /t 5 >nul

echo.
echo [2/2] Menjalankan Ngrok Tunnel...
echo.
echo ========================================================
echo   PENTING:
echo   1. Jendela baru akan terbuka.
echo   2. Cari tulisan "Forwarding": https://xxxx.ngrok-free.app
echo   3. COPY link tersebut dan buka di Browser HP.
echo ========================================================
echo.

REM Jalankan Ngrok di window baru
start "Ngrok Tunnel" cmd /k "ngrok.exe http 8000"

pause
