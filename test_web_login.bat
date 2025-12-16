@echo off
echo =========================================
echo    WEB LOGIN TEST - AUTOMATED
echo =========================================
echo.

echo [1] Testing Login Page Access...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8000/login
echo.

echo [2] Testing Login - Siswa1 (Valid)...
echo.
curl -X POST http://localhost:8000/login ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "username=siswa1&password=password123&role=siswa" ^
  -L -i -s | findstr /C:"HTTP" /C:"Location" /C:"error" /C:"success" /C:"Login"
echo.

echo =========================================
echo.

echo [3] Testing Login - Guru1 (Valid)...
echo.
curl -X POST http://localhost:8000/login ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "username=guru1&password=password123&role=guru" ^
  -L -i -s | findstr /C:"HTTP" /C:"Location" /C:"error" /C:"success"
echo.

echo =========================================
echo.

echo [4] Testing Login - Wrong Password...
echo.
curl -X POST http://localhost:8000/login ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "username=siswa1&password=wrongpass&role=siswa" ^
  -L -i -s | findstr /C:"HTTP" /C:"Location" /C:"error" /C:"salah"
echo.

echo =========================================
echo    TEST COMPLETED
echo =========================================
pause
