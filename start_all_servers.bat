@echo off
color 0A
title START ALL SERVERS - Laravel + Cloudflared
echo.
echo ========================================
echo   STARTING ALL SERVERS AUTOMATICALLY
echo ========================================
echo.
echo [1/2] Starting Laravel Server...
echo.

REM Start Laravel Server di window baru
start "Laravel Server - Port 8000" cmd /k "cd /d d:\tubes web pix100 && echo ======================================== && echo   LARAVEL SERVER RUNNING && echo ======================================== && echo. && echo Server URL: http://0.0.0.0:8000 && echo Local: http://localhost:8000 && echo Network: http://192.168.18.213:8000 && echo. && echo JANGAN TUTUP WINDOW INI! && echo. && php artisan serve --host=0.0.0.0 --port=8000"

REM Tunggu 3 detik agar Laravel server start dulu
echo Waiting for Laravel to start...
timeout /t 3 /nobreak >nul

echo.
echo [2/2] Starting Cloudflared Tunnel...
echo.

REM Start Cloudflared di window baru  
start "Cloudflared Tunnel - HTTPS" cmd /k "cd /d C:\cloudflared && echo ======================================== && echo   CLOUDFLARED TUNNEL STARTING && echo ======================================== && echo. && echo Tunggu URL HTTPS muncul... && echo COPY URL tersebut untuk mobile! && echo. && echo JANGAN TUTUP WINDOW INI! && echo. && cloudflared.exe tunnel --url http://localhost:8000"

echo.
echo ========================================
echo   DONE! 2 WINDOWS TERBUKA:
echo ========================================
echo.
echo Window 1: Laravel Server
echo   - Local: http://localhost:8000
echo   - Network: http://192.168.18.213:8000
echo.
echo Window 2: Cloudflared Tunnel
echo   - Tunggu URL HTTPS muncul!
echo   - Copy URL: https://xxx.trycloudflare.com
echo.
echo ========================================
echo   NEXT STEPS:
echo ========================================
echo.
echo 1. Lihat Window "Cloudflared Tunnel"
echo 2. Tunggu sampai muncul URL HTTPS
echo 3. Copy URL: https://xxx.trycloudflare.com
echo 4. Test di browser: URL/login
echo 5. Buka di HP untuk scan QR!
echo.
echo JANGAN TUTUP KEDUA WINDOW TERSEBUT!
echo.
pause
