@echo off
title MyAcademy - Fix Certificates
color 0E

echo.
echo ========================================
echo   MyAcademy - Certificate Setup Fix
echo ========================================
echo.

cd /d "%~dp0"

echo [1/6] Checking GD extension...
php -m | findstr "gd" >nul
if %errorlevel% neq 0 (
    echo [WARNING] GD extension not enabled!
    echo Please enable it in your php.ini file
    pause
    exit /b 1
) else (
    echo [OK] GD extension is enabled
)

echo.
echo [2/6] Installing dependencies...
php composer.phar install --no-dev --optimize-autoloader

echo.
echo [3/6] Creating storage symlink...
php artisan storage:link

echo.
echo [4/6] Creating directories...
if not exist "public\certificates\templates" mkdir "public\certificates\templates"
if not exist "storage\app\certificates" mkdir "storage\app\certificates"

echo.
echo [5/6] Running migrations...
php artisan migrate --force

echo.
echo [6/6] Clearing all caches...
php artisan optimize:clear

echo.
echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo Certificate feature is ready!
echo.
echo Now run: START_SERVER.bat
echo Then go to: http://127.0.0.1:8000/certificates
echo Login as: admin or teacher
echo.
pause
