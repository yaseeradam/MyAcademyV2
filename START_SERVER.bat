@echo off
title MyAcademy - Server
color 0B

cd /d "%~dp0"

echo.
echo ========================================
echo       MyAcademy School System
echo ========================================
echo.
echo Starting server at http://127.0.0.1:8000
echo Press Ctrl+C to stop
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
