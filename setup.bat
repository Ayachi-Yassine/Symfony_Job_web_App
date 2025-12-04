@echo off
REM JobEntry - Quick Start Script for Windows
REM This script sets up and runs the JobEntry Symfony application

echo.
echo ================================================
echo   JobEntry - Job Portal Setup
echo ================================================
echo.

REM Check if Composer is installed
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo ERROR: Composer is not installed!
    echo Please download and install Composer from: https://getcomposer.org/download/
    pause
    exit /b 1
)

REM Check if PHP is installed
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed!
    echo Please download and install PHP 8.1+ from: https://www.php.net/downloads
    pause
    exit /b 1
)

echo [1/5] Checking MySQL connection...
mysql -u root -e "SELECT 1" >nul 2>nul
if %errorlevel% neq 0 (
    echo WARNING: Could not connect to MySQL with user 'root'
    echo Make sure MySQL server is running!
    echo.
)

echo [2/5] Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo [3/5] Creating database...
php bin/console doctrine:database:create --if-not-exists
if %errorlevel% neq 0 (
    echo ERROR: Database creation failed!
    pause
    exit /b 1
)

echo [4/5] Running migrations...
php bin/console doctrine:migrations:migrate --no-interaction
if %errorlevel% neq 0 (
    echo ERROR: Migrations failed!
    pause
    exit /b 1
)

echo [5/5] Clearing cache...
php bin/console cache:clear

echo.
echo ================================================
echo   ✓ Setup Complete!
echo ================================================
echo.
echo You can now start the development server with:
echo   symfony server:start
echo.
echo Or using PHP directly:
echo   php -S localhost:8000 -t public
echo.
echo Then open your browser and visit:
echo   http://localhost:8000
echo.
pause
