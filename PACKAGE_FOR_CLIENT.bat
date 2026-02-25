@echo off
setlocal enabledelayedexpansion
title MyAcademy - Package for Client
color 0A

echo.
echo ========================================
echo   MyAcademy - Package for Client
echo ========================================
echo.

:: Get current directory name
for %%I in (.) do set CURRENT_DIR=%%~nxI

:: Set package name with timestamp
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,8%-%datetime:~8,6%
set PACKAGE_NAME=MyAcademy-Package-%TIMESTAMP%

echo [1/6] Creating package directory...
set PACKAGE_DIR=..\%PACKAGE_NAME%
if exist "%PACKAGE_DIR%" (
    echo Removing old package directory...
    rmdir /s /q "%PACKAGE_DIR%"
)
mkdir "%PACKAGE_DIR%"

echo.
echo [2/6] Copying application files...
xcopy /E /I /H /Y /EXCLUDE:package-exclude.txt . "%PACKAGE_DIR%" >nul 2>&1

:: Create exclude list on the fly
echo node_modules\ > package-exclude.txt
echo vendor\ >> package-exclude.txt
echo .git\ >> package-exclude.txt
echo storage\logs\ >> package-exclude.txt
echo storage\framework\cache\ >> package-exclude.txt
echo storage\framework\sessions\ >> package-exclude.txt
echo storage\framework\views\ >> package-exclude.txt
echo .env >> package-exclude.txt
echo .env.backup >> package-exclude.txt
echo public\build\ >> package-exclude.txt
echo public\hot >> package-exclude.txt

:: Copy with exclusions
robocopy . "%PACKAGE_DIR%" /E /XD node_modules vendor .git storage\logs storage\framework\cache storage\framework\sessions storage\framework\views public\build /XF .env .env.backup public\hot package-exclude.txt /NFL /NDL /NJH /NJS /nc /ns /np

echo.
echo [3/6] Creating .env.example for client...
(
echo APP_NAME="MyAcademy"
echo APP_ENV=production
echo APP_KEY=
echo APP_DEBUG=false
echo APP_TIMEZONE=UTC
echo APP_URL=http://localhost
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=myacademy
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo MYACADEMY_ADMIN_EMAIL=admin@myacademy.local
echo MYACADEMY_ADMIN_PASSWORD=password
echo.
echo SESSION_DRIVER=file
echo SESSION_LIFETIME=120
echo.
echo CACHE_STORE=file
echo.
echo LOG_CHANNEL=daily
echo LOG_LEVEL=error
) > "%PACKAGE_DIR%\.env.example"

echo.
echo [4/6] Creating empty required directories...
if not exist "%PACKAGE_DIR%\storage\logs" mkdir "%PACKAGE_DIR%\storage\logs"
if not exist "%PACKAGE_DIR%\storage\framework\cache\data" mkdir "%PACKAGE_DIR%\storage\framework\cache\data"
if not exist "%PACKAGE_DIR%\storage\framework\sessions" mkdir "%PACKAGE_DIR%\storage\framework\sessions"
if not exist "%PACKAGE_DIR%\storage\framework\views" mkdir "%PACKAGE_DIR%\storage\framework\views"
if not exist "%PACKAGE_DIR%\public\uploads" mkdir "%PACKAGE_DIR%\public\uploads"

:: Create .gitkeep files
echo. > "%PACKAGE_DIR%\storage\logs\.gitkeep"
echo. > "%PACKAGE_DIR%\storage\framework\cache\data\.gitkeep"
echo. > "%PACKAGE_DIR%\storage\framework\sessions\.gitkeep"
echo. > "%PACKAGE_DIR%\storage\framework\views\.gitkeep"
echo. > "%PACKAGE_DIR%\public\uploads\.gitkeep"

echo.
echo [5/6] Creating CLIENT_SETUP.txt instructions...
(
echo ========================================
echo   MyAcademy - Client Setup Guide
echo ========================================
echo.
echo QUICK START:
echo.
echo 1. Install Laragon from https://laragon.org/download/
echo.
echo 2. Copy this entire folder to: C:\laragon\www\
echo.
echo 3. Double-click MYACADEMY.bat and select option 1
echo.
echo 4. Access the system at: http://localhost or http://YOUR-IP
echo.
echo ========================================
echo.
echo DEFAULT LOGIN:
echo   Email: admin@myacademy.local
echo   Password: password
echo.
echo IMPORTANT: Change the admin password after first login!
echo.
echo ========================================
echo.
echo TROUBLESHOOTING:
echo.
echo - If certificates don't work, run: ENABLE_GD.bat
echo - To check system status, run: CHECK_SYSTEM.bat
echo - To view network info, run: NETWORK_INFO.bat
echo.
echo For full documentation, see QUICK_START.md
echo.
) > "%PACKAGE_DIR%\CLIENT_SETUP.txt"

echo.
echo [6/6] Creating ZIP package...
powershell -command "Compress-Archive -Path '%PACKAGE_DIR%\*' -DestinationPath '..\%PACKAGE_NAME%.zip' -Force"

if exist "..\%PACKAGE_NAME%.zip" (
    echo.
    echo ========================================
    echo   SUCCESS!
    echo ========================================
    echo.
    echo Package created: %PACKAGE_NAME%.zip
    echo Location: %CD%\..\%PACKAGE_NAME%.zip
    echo.
    echo Cleaning up temporary files...
    rmdir /s /q "%PACKAGE_DIR%"
    del package-exclude.txt >nul 2>&1
    echo.
    echo Ready to send to client!
    echo.
) else (
    echo.
    echo ERROR: Failed to create ZIP package.
    echo The uncompressed package is available at: %PACKAGE_DIR%
    echo.
)

echo Press any key to exit...
pause >nul
