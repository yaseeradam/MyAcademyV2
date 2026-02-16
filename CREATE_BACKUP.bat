@echo off
title MyAcademy - Create Backup
color 0E

echo.
echo ========================================
echo      MyAcademy Backup Creator
echo ========================================
echo.

cd /d C:\laragon\www\myacademy-laravel

echo [*] Creating backup...
echo.

REM Create backup using Laravel command
php artisan backup:run

if %errorLevel% equ 0 (
    echo.
    echo [OK] Backup created successfully!
    echo.
    echo Backup location:
    echo   C:\laragon\www\myacademy-laravel\storage\app\backups\
    echo.
    echo IMPORTANT: Copy this backup to an external drive!
) else (
    echo.
    echo [ERROR] Backup failed!
    echo Please check if the system is running properly.
)

echo.
pause
