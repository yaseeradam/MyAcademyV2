@echo off
title MyAcademy - System Check
color 0A

echo.
echo ========================================
echo    MyAcademy System Diagnostics
echo ========================================
echo.

cd /d C:\laragon\www\myacademy-laravel

echo [*] Checking Laragon...
if exist "C:\laragon\laragon.exe" (
    echo [OK] Laragon installed
) else (
    echo [ERROR] Laragon not found!
)

echo.
echo [*] Checking PHP...
php -v >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] PHP is working
    php -v | findstr /C:"PHP"
) else (
    echo [ERROR] PHP not found!
)

echo.
echo [*] Checking Composer...
php composer.phar --version >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Composer is working
) else (
    echo [ERROR] Composer not found!
)

echo.
echo [*] Checking Node.js...
node -v >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Node.js is working
    node -v
) else (
    echo [ERROR] Node.js not found!
)

echo.
echo [*] Checking database connection...
php artisan db:show >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Database connected
) else (
    echo [ERROR] Database connection failed!
)

echo.
echo [*] Checking .env file...
if exist ".env" (
    echo [OK] .env file exists
) else (
    echo [ERROR] .env file not found!
)

echo.
echo [*] Checking storage permissions...
if exist "storage\logs" (
    echo [OK] Storage directory exists
) else (
    echo [ERROR] Storage directory not found!
)

echo.
echo [*] Checking uploads directory...
if exist "public\uploads" (
    echo [OK] Uploads directory exists
) else (
    echo [WARNING] Uploads directory not found - will be created automatically
)

echo.
echo ========================================
echo    System Status Summary
echo ========================================
echo.
echo If all checks passed, your system is ready!
echo If any errors, please:
echo   1. Run INSTALL.bat again
echo   2. Check INSTALLATION_GUIDE.md
echo   3. Contact support
echo.
pause
