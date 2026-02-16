@echo off
title MyAcademy - Starting Server
color 0B

REM Get local IP address
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP=%%a
    goto :found
)
:found
set IP=%IP:~1%

echo.
echo ========================================
echo       MyAcademy School System
echo ========================================
echo.
echo [*] Starting Laragon services...
cd /d C:\laragon
start /min laragon.exe start
timeout /t 3 /nobreak >nul

echo [*] Starting MyAcademy server...
cd /d C:\laragon\www\myacademy-laravel

REM Check if .env exists
if not exist ".env" (
    echo [ERROR] .env file not found!
    echo Please run INSTALL.bat first
    pause
    exit /b 1
)

echo.
echo ========================================
echo    Server Started Successfully!
echo ========================================
echo.
echo Access URLs:
echo   Admin Laptop:  http://127.0.0.1:8000
echo   Teachers:      http://%IP%:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

REM Start server
php artisan serve --host=0.0.0.0 --port=8000

pause
