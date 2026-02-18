@echo off
title Enable GD Extension for MyAcademy
color 0E

echo.
echo ========================================
echo    Enabling GD Extension for PHP
echo ========================================
echo.

REM Find php.ini in Laragon
set PHP_INI=C:\laragon\bin\php\php-8.2.28\php.ini

if not exist "%PHP_INI%" (
    echo [ERROR] php.ini not found at: %PHP_INI%
    echo.
    echo Please check your Laragon PHP installation path.
    pause
    exit /b 1
)

echo [*] Found php.ini at: %PHP_INI%
echo.

REM Check if GD is already enabled
findstr /C:"extension=gd" "%PHP_INI%" | findstr /V "^;" >nul
if %errorlevel% equ 0 (
    echo [OK] GD extension is already enabled!
    echo.
    pause
    exit /b 0
)

echo [*] Enabling GD extension...

REM Backup php.ini
copy "%PHP_INI%" "%PHP_INI%.backup" >nul
echo [*] Backup created: %PHP_INI%.backup

REM Enable GD extension
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=gd', 'extension=gd' | Set-Content '%PHP_INI%'"

echo [*] GD extension enabled successfully!
echo.
echo ========================================
echo    IMPORTANT: Restart Laragon
echo ========================================
echo.
echo Please restart Laragon for changes to take effect:
echo 1. Stop Laragon (right-click tray icon ^> Stop All)
echo 2. Start Laragon again
echo 3. Run START_MYACADEMY.bat
echo.

pause
