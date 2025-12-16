@echo off
echo ================================================
echo    SETUP TESTING DI HANDPHONE
echo ================================================
echo.

echo [1] Download Ngrok
echo [2] Setup dengan Local Network (WiFi sama)
echo [3] Exit
echo.

set /p choice="Pilih cara (1/2/3): "

if "%choice%"=="1" goto ngrok
if "%choice%"=="2" goto local
if "%choice%"=="3" goto end

:ngrok
echo.
echo ================================================
echo    SETUP NGROK
echo ================================================
echo.
echo Step 1: Download Ngrok
echo Membuka browser untuk download ngrok...
start https://ngrok.com/download
echo.
echo Step 2: Setelah download:
echo   1. Extract ngrok.exe ke folder ini
echo   2. Atau taruh di C:\ngrok\
echo.
echo Step 3: Sign up di ngrok.com dan copy authtoken
start https://dashboard.ngrok.com/signup
echo.
echo Step 4: Setelah dapat authtoken, jalankan:
echo   ngrok config add-authtoken YOUR_TOKEN
echo.
echo Step 5: Jalankan script: start_ngrok.bat
echo.
pause
goto end

:local
echo.
echo ================================================
echo    SETUP LOCAL NETWORK
echo ================================================
echo.
echo Mendapatkan IP Address komputer Anda...
echo.
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP=%%a
    set IP=!IP:~1!
    echo IP Address Anda: !IP!
)
echo.
echo Langkah selanjutnya:
echo 1. Pastikan HP dan Laptop terhubung ke WiFi YANG SAMA
echo 2. Jalankan: php artisan serve --host=0.0.0.0 --port=8000
echo 3. Allow firewall jika diminta
echo 4. Buka di HP: http://!IP!:8000
echo.
echo Membuat script untuk start Laravel...
echo.

echo @echo off > start_local.bat
echo echo Starting Laravel for Local Network... >> start_local.bat
echo php artisan serve --host=0.0.0.0 --port=8000 >> start_local.bat

echo.
echo Script dibuat! Jalankan: start_local.bat
echo.
pause
goto end

:end
echo.
echo Terima kasih!
pause
