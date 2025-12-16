@echo off
echo ========================================
echo CLOUDFLARED WITH URL DISPLAY
echo ========================================
echo.
echo Starting Cloudflared tunnel...
echo URL akan ditampilkan di bawah!
echo.
echo ========================================
echo.

cd "d:\tubes web pix100"

REM Run cloudflared dan tampilkan output
C:\cloudflared\cloudflared.exe tunnel --url http://localhost:8000

pause
