@echo off
title MyAcademy - Stopping Server
color 0C

echo.
echo ========================================
echo    Stopping MyAcademy Server
echo ========================================
echo.

echo [*] Stopping PHP server...
taskkill /F /IM php.exe >nul 2>&1

echo [*] Stopping Laragon services...
cd /d C:\laragon
laragon.exe stop

echo.
echo [OK] Server stopped successfully!
echo.
pause
