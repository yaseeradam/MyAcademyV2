@echo off
title MyAcademy - Install Certificate Feature
color 0A

echo.
echo ========================================
echo   MyAcademy Certificate Feature Setup
echo ========================================
echo.
echo This will install the certificate generation feature.
echo.
pause

echo.
echo [1/3] Installing Intervention Image package...
echo.
php composer.phar update

echo.
echo [2/3] Running database migration...
echo.
php artisan migrate --force

echo.
echo [3/3] Clearing cache...
echo.
php artisan cache:clear

echo.
echo ========================================
echo   Installation Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Read CANVA_CERTIFICATE_GUIDE.md to design templates
echo 2. Go to More Features -^> Certificates
echo 3. Upload your certificate templates
echo 4. Generate certificates for students
echo.
echo See CERTIFICATE_FEATURE_README.md for full documentation.
echo.
pause
