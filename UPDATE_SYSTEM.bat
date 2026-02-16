@echo off
title MyAcademy - System Update
color 0D

echo.
echo ========================================
echo      MyAcademy System Updater
echo ========================================
echo.
echo WARNING: This will update your system!
echo Make sure you have a backup before proceeding.
echo.
set /p confirm="Continue? (Y/N): "
if /i not "%confirm%"=="Y" (
    echo Update cancelled.
    pause
    exit /b 0
)

cd /d C:\laragon\www\myacademy-laravel

echo.
echo [1/5] Stopping server...
taskkill /F /IM php.exe >nul 2>&1
echo [OK] Server stopped

echo.
echo [2/5] Updating dependencies...
php composer.phar install --no-interaction --prefer-dist
call npm install
echo [OK] Dependencies updated

echo.
echo [3/5] Running database migrations...
php artisan migrate --force
echo [OK] Database updated

echo.
echo [4/5] Rebuilding assets...
call npm run build
echo [OK] Assets rebuilt

echo.
echo [5/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo [OK] Cache cleared

echo.
echo ========================================
echo    Update Complete!
echo ========================================
echo.
echo You can now start the system using START_MYACADEMY.bat
echo.
pause
