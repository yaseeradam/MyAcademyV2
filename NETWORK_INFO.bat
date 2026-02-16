@echo off
title MyAcademy - Network Information
color 0F

echo.
echo ========================================
echo    MyAcademy Network Information
echo ========================================
echo.

REM Get local IP address
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP=%%a
    goto :found
)
:found
set IP=%IP:~1%

echo Your Computer's IP Address: %IP%
echo.
echo ========================================
echo    Teacher Access Instructions
echo ========================================
echo.
echo 1. Make sure teachers are connected to the same WiFi
echo 2. Teachers should open their browser and go to:
echo.
echo    http://%IP%:8000
echo.
echo 3. They can bookmark this URL for easy access
echo.
echo ========================================
echo    QR Code for Easy Sharing
echo ========================================
echo.
echo You can create a QR code for this URL:
echo http://%IP%:8000
echo.
echo Use any QR code generator website to create it
echo and print it for teachers to scan.
echo.
pause
