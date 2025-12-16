@echo off
echo ========================================
echo FORCE LARAVEL SERVER - MOBILE ACCESS
echo ========================================
echo.
echo Stopping any existing server...
taskkill /F /IM php.exe 2>nul
timeout /t 2 >nul
echo.
echo Starting server on ALL network interfaces...
echo IP: 192.168.18.208
echo Port: 8000
echo.
echo Server akan accessible dari:
echo - Laptop: http://localhost:8000
echo - Mobile: http://192.168.18.208:8000
echo.
echo Press Ctrl+C to stop
echo ========================================
echo.

cd "d:\tubes web pix100"
php -S 0.0.0.0:8000 -t public
