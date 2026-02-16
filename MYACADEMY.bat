@echo off
title MyAcademy Control Panel
color 0B
mode con: cols=80 lines=30

:MENU
cls
echo.
echo     ========================================================================
echo     .                                                                  
echo     .                M Y A C A D E M Y                              
echo     .                                                                  
echo     .                 Control Panel v1.0                            
echo     .                                                                  
echo     ========================================================================
echo.
echo     ------------------------------------------------------------------------
echo     .                          MAIN MENU                              
echo     ------------------------------------------------------------------------
echo     .                                                                  
echo     .     [1] Install System          (First Time Only)       
echo     .                                                                  
echo     .     [2] Start Server             (Daily Use)             
echo     .                                                                  
echo     .     [3] Stop Server              (End of Day)            
echo     .                                                                  
echo     .     [4] Create Backup            (Weekly)                
echo     .                                                                  
echo     .     [5] Update System            (New Version)           
echo     .                                                                  
echo     .     [6] Network Info             (Teacher Access)        
echo     .                                                                  
echo     .     [7] System Check             (Diagnostics)           
echo     .                                                                  
echo     .     [8] Exit                                              
echo     .                                                                  
echo     ------------------------------------------------------------------------
echo.
set /p choice="     Select option (1-8): "

if "%choice%"=="1" goto INSTALL
if "%choice%"=="2" goto START
if "%choice%"=="3" goto STOP
if "%choice%"=="4" goto BACKUP
if "%choice%"=="5" goto UPDATE
if "%choice%"=="6" goto NETWORK
if "%choice%"=="7" goto CHECK
if "%choice%"=="8" goto EXIT
goto MENU

:INSTALL
cls
echo.
echo     ========================================================================
echo     .                  INSTALLING MYACADEMY SYSTEM                   
echo     ========================================================================
echo.
echo     ⚠  WARNING: This will install the system!
echo     Make sure Laragon is installed first.
echo.
set /p confirm="     Continue? (Y/N): "
if /i not "%confirm%"=="Y" goto MENU

net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo [ERROR] Administrator rights required!
    echo Please run this file as Administrator.
    pause
    goto MENU
)

echo.
echo     [1/7] Checking Laragon...
if not exist "C:\laragon\laragon.exe" (
    echo [ERROR] Laragon not found!
    echo Install from: https://laragon.org/download/
    pause
    goto MENU
)
echo     [OK] Laragon found

echo.
echo     [2/7] Starting Laragon...
cd /d C:\laragon
start /wait laragon.exe start
timeout /t 5 /nobreak >nul
echo     [OK] Services started

echo.
echo     [3/7] Creating database...
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS myacademy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo     [OK] Database created

echo.
echo     [4/7] Installing PHP dependencies...
cd /d C:\laragon\www\myacademy-laravel
php composer.phar install --no-interaction --prefer-dist
echo     [OK] PHP dependencies installed

echo.
echo     [5/7] Installing JavaScript dependencies...
call npm install
echo     [OK] JavaScript dependencies installed

echo.
echo     [6/7] Building assets...
call npm run build
echo     [OK] Assets built

echo.
echo     [7/7] Setting up database...
php artisan migrate --force
php artisan db:seed --force
echo     [OK] Database setup complete
echo.
echo     ========================================================================
echo     .                    INSTALLATION COMPLETE!                           
echo     ========================================================================
echo.
echo     Default Login: admin@myacademy.local / password
echo     Next: Use option [2] to start the server
echo.
pause
goto MENU

:START
cls
echo.
echo     ========================================================================
echo     .                    STARTING MYACADEMY SERVER                         
echo     ========================================================================
echo.

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP=%%a
    goto :ip_found
)
:ip_found
set IP=%IP:~1%

echo [*] Starting Laragon...
cd /d C:\laragon
start /min laragon.exe start
timeout /t 3 /nobreak >nul

cd /d C:\laragon\www\myacademy-laravel

if not exist ".env" (
    echo [ERROR] System not installed!
    echo Please run option 1 first.
    pause
    goto MENU
)

echo.
echo     ========================================================================
echo     .                       SERVER RUNNING!                                
echo     ========================================================================
echo.
echo     Admin Access:    http://127.0.0.1:8000
echo     Teacher Access:  http://%IP%:8000
echo.
echo     Press Ctrl+C to stop, then press any key
echo     ------------------------------------------------------------------------
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
goto MENU

:STOP
cls
echo.
echo     ========================================================================
echo     .                         STOPPING SERVER                                  
echo     ========================================================================
echo.

echo     [✓] Stopping server...
taskkill /F /IM php.exe >nul 2>&1

echo     [✓] Stopping Laragon...
cd /d C:\laragon
laragon.exe stop

echo.
echo     ========================================================================
echo     .                         SERVER STOPPED                               
echo     ========================================================================
echo.
pause
goto MENU

:BACKUP
cls
echo.
echo     ========================================================================
echo     .                         CREATING BACKUP                                  
echo     ========================================================================
echo.

cd /d C:\laragon\www\myacademy-laravel

echo     Creating backup...
php artisan backup:run

if %errorLevel% equ 0 (
    echo.
    echo     ========================================================================
    echo     .                       BACKUP CREATED!                                
    echo     ========================================================================
    echo.
    echo     Location: storage\app\backups\
    echo     IMPORTANT: Copy to external drive!
) else (
    echo.
    echo     ========================================================================
    echo     .                         BACKUP FAILED                                
    echo     ========================================================================
)

echo.
pause
goto MENU

:UPDATE
cls
echo.
echo ========================================
echo    Updating System
echo ========================================
echo.
echo WARNING: Backup first!
echo.
set /p confirm="Continue? (Y/N): "
if /i not "%confirm%"=="Y" goto MENU

cd /d C:\laragon\www\myacademy-laravel

echo.
echo [1/5] Stopping server...
taskkill /F /IM php.exe >nul 2>&1
echo [OK] Stopped

echo.
echo [2/5] Updating dependencies...
php composer.phar install --no-interaction
call npm install
echo [OK] Updated

echo.
echo [3/5] Migrating database...
php artisan migrate --force
echo [OK] Migrated

echo.
echo [4/5] Building assets...
call npm run build
echo [OK] Built

echo.
echo [5/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
echo [OK] Cleared

echo.
echo [OK] Update complete!
echo.
pause
goto MENU

:NETWORK
cls
echo.
echo     ========================================================================
echo     .                        NETWORK INFORMATION                               
echo     ========================================================================
echo.

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP=%%a
    goto :ip_found2
)
:ip_found2
set IP=%IP:~1%

echo     Your IP Address: %IP%
echo.
echo     ------------------------------------------------------------------------
echo     .     Teacher Access URL:                                           
echo     .                                                                
echo     .         http://%IP%:8000                                    
echo     ------------------------------------------------------------------------
echo.
echo     Instructions:
echo       1. Teachers connect to same WiFi
echo       2. Open browser and go to URL above
echo       3. Bookmark for easy access
echo.
pause
goto MENU

:CHECK
cls
echo.
echo     ========================================================================
echo     .                        SYSTEM DIAGNOSTICS                                
echo     ========================================================================
echo.

cd /d C:\laragon\www\myacademy-laravel

echo     Checking Laragon...
if exist "C:\laragon\laragon.exe" (
    echo     [OK] Laragon installed
) else (
    echo     [ERROR] Laragon not found!
)

echo.
echo     Checking PHP...
php -v >nul 2>&1
if %errorLevel% equ 0 (
    echo     [OK] PHP working
) else (
    echo     [ERROR] PHP not found!
)

echo.
echo     Checking Node.js...
node -v >nul 2>&1
if %errorLevel% equ 0 (
    echo     [OK] Node.js working
) else (
    echo     [ERROR] Node.js not found!
)

echo.
echo     Checking database...
php artisan db:show >nul 2>&1
if %errorLevel% equ 0 (
    echo     [OK] Database connected
) else (
    echo     [ERROR] Database failed!
)

echo.
echo     Checking .env...
if exist ".env" (
    echo     [OK] Config file exists
) else (
    echo     [ERROR] .env not found!
)

echo.
echo     ========================================================================
echo     .                        DIAGNOSTICS COMPLETE                              
echo     ========================================================================
echo.
pause
goto MENU

:EXIT
cls
echo.
echo     ========================================================================
echo     .                                                                  
echo     .                Thank you for using MyAcademy!                      
echo     .                                                                  
echo     .                    School Management Made Easy                     
echo     .                                                                  
echo     ========================================================================
echo.
timeout /t 2 /nobreak >nul
exit

