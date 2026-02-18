@echo off
title Fix MySQL Authentication
color 0E

echo.
echo ========================================
echo   Fix MySQL Authentication Plugin
echo ========================================
echo.

REM Find mysql.exe
set MYSQL_PATH=
if exist "C:\laragon\bin\mysql\mysql-8.0.30\bin\mysql.exe" set MYSQL_PATH=C:\laragon\bin\mysql\mysql-8.0.30\bin\mysql.exe
if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" set MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe
if exist "C:\Program Files\MySQL\MySQL Server 9.5\bin\mysql.exe" set MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 9.5\bin\mysql.exe

if "%MYSQL_PATH%"==" " (
    echo [ERROR] MySQL not found!
    echo Please enter the full path to mysql.exe
    pause
    exit /b 1
)

echo [*] Using: %MYSQL_PATH%
echo.
echo [*] Fixing authentication...
echo.

"%MYSQL_PATH%" -u root -p -e "ALTER USER 'myacademy'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'Myacademy@2026!'; ALTER USER 'myacademy'@'%%' IDENTIFIED WITH caching_sha2_password BY 'Myacademy@2026!'; FLUSH PRIVILEGES;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCESS] Authentication fixed!
    echo [*] Now restart your server with START_MYACADEMY.bat
) else (
    echo.
    echo [ERROR] Failed to fix authentication
    echo Try running Laragon's MySQL console and run:
    echo ALTER USER 'myacademy'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'Myacademy@2026!';
    echo FLUSH PRIVILEGES;
)

echo.
pause
