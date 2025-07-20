@echo off
echo ===================================
echo    SB Farm Database Fresh Script
echo ===================================
echo.

echo Running database fresh script...
php database_fresh.php

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ===================================
    echo    Database Fresh Completed!
    echo ===================================
    echo.
    echo You can now start the server with:
    echo php -S localhost:8000 -t public
) else (
    echo.
    echo ===================================
    echo    Database Fresh Failed!
    echo ===================================
    echo Please check the error messages above.
)

echo.
pause