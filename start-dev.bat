@echo off
title MyAcademy Development Server
color 0A

echo ========================================
echo   MyAcademy Development Server
echo ========================================
echo.
echo Starting Laravel server and Vite...
echo.

start "Vite Dev Server" cmd /k "npm run dev"
timeout /t 3 /nobreak >nul
start "Laravel Server" cmd /k "php artisan serve"

echo.
echo ========================================
echo   Servers Started!
echo ========================================
echo.
echo Vite:    Running in separate window
echo Laravel: http://127.0.0.1:8000
echo.
echo Press any key to stop all servers...
pause >nul

taskkill /FI "WindowTitle eq Vite Dev Server*" /T /F >nul 2>&1
taskkill /FI "WindowTitle eq Laravel Server*" /T /F >nul 2>&1

echo.
echo Servers stopped.
timeout /t 2 /nobreak >nul
