@echo off
title MyAcademy - One-Click Installer
color 0A
echo.
echo ========================================
echo    MyAcademy Installation Wizard
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Please run as Administrator!
    echo Right-click this file and select "Run as administrator"
    pause
    exit /b 1
)

echo [1/7] Checking Laragon installation...
if not exist "C:\laragon\laragon.exe" (
    echo [ERROR] Laragon not found!
    echo Please install Laragon first from: https://laragon.org/download/
    pause
    exit /b 1
)
echo [OK] Laragon found

echo.
echo [2/7] Starting Laragon services...
cd /d C:\laragon
start /wait laragon.exe start
timeout /t 5 /nobreak >nul
echo [OK] Services started

echo.
echo [3/7] Creating database...
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS myacademy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %errorLevel% neq 0 (
    echo [ERROR] Database creation failed!
    pause
    exit /b 1
)
echo [OK] Database created

echo.
echo [4/7] Installing PHP dependencies...
cd /d C:\laragon\www\myacademy-laravel
php composer.phar install --no-interaction --prefer-dist
if %errorLevel% neq 0 (
    echo [ERROR] Composer install failed!
    pause
    exit /b 1
)
echo [OK] PHP dependencies installed

echo.
echo [5/7] Installing JavaScript dependencies...
call npm install
if %errorLevel% neq 0 (
    echo [ERROR] NPM install failed!
    pause
    exit /b 1
)
echo [OK] JavaScript dependencies installed

echo.
echo [6/7] Building assets...
call npm run build
if %errorLevel% neq 0 (
    echo [ERROR] Asset build failed!
    pause
    exit /b 1
)
echo [OK] Assets built

echo.
echo [7/7] Setting up database...
php artisan migrate --force
php artisan db:seed --force
if %errorLevel% neq 0 (
    echo [ERROR] Database setup failed!
    pause
    exit /b 1
)
echo [OK] Database setup complete

echo.
echo ========================================
echo    Installation Complete!
echo ========================================
echo.
echo Default Login Credentials:
echo   Admin:   admin@myacademy.local / password
echo   Teacher: teacher@myacademy.local / password
echo   Bursar:  bursar@myacademy.local / password
echo.
echo Next Steps:
echo   1. Double-click "START_MYACADEMY.bat" to start the system
echo   2. Open browser: http://127.0.0.1:8000
echo   3. Login and change default passwords
echo.
pause
