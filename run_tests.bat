@echo off
echo ========================================
echo     SB FARM API TEST RUNNER
echo ========================================
echo.

echo Checking if server is running...
netstat -an | find "8000" >nul
if %errorlevel% neq 0 (
    echo Server is not running on port 8000!
    echo Please start the server first with: php -S localhost:8000 -t public
    echo.
    pause
    exit /b 1
)

echo Server is running on port 8000
echo.

echo Choose test option:
echo 1. Run PHPUnit Tests (Recommended)
echo 2. Run Custom Test Suite
echo 3. Run Quick Test
echo 4. Run All Tests
echo.
set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" (
    echo.
    echo Running PHPUnit Tests...
    echo ========================================
    vendor\bin\phpunit tests\ApiCrudTest.php --verbose
) else if "%choice%"=="2" (
    echo.
    echo Running Custom Test Suite...
    echo ========================================
    php run_api_tests.php
) else if "%choice%"=="3" (
    echo.
    echo Running Quick Test...
    echo ========================================
    php run_api_tests.php quick
) else if "%choice%"=="4" (
    echo.
    echo Running All Tests...
    echo ========================================
    echo.
    echo 1. PHPUnit Tests:
    echo ----------------------------------------
    vendor\bin\phpunit tests\ApiCrudTest.php --verbose
    echo.
    echo 2. Custom Test Suite:
    echo ----------------------------------------
    php run_api_tests.php
) else (
    echo Invalid choice!
    pause
    exit /b 1
)

echo.
echo ========================================
echo Tests completed!
echo ========================================
echo.
pause