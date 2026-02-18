@echo off
title Enable GD Extension
color 0E

echo.
echo ========================================
echo    Enabling GD Extension
echo ========================================
echo.

cd /d "%~dp0"

echo Finding php.ini location...
for /f "delims=" %%i in ('php --ini ^| findstr "Loaded Configuration File"') do set PHP_INI_LINE=%%i
for /f "tokens=2* delims=:" %%a in ("%PHP_INI_LINE%") do set PHP_INI=%%a
set PHP_INI=%PHP_INI:~1%

if not exist "%PHP_INI%" (
    echo [ERROR] php.ini not found!
    echo Please enable GD manually in your php.ini
    pause
    exit /b 1
)

echo Found: %PHP_INI%
echo.

echo Enabling GD extension...
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=gd', 'extension=gd' | Set-Content '%PHP_INI%'"

echo.
echo [OK] GD extension enabled!
echo.
echo Now run: FIX_CERTIFICATES.bat
echo.
pause
