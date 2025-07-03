@echo off
REM Set the path to your PHP executable in XAMPP
set PHP_PATH="C:\xampp\php\php.exe"

REM Navigate to your Laravel project directory
cd /d "C:\xampp\htdocs\dukani"

REM Run the Laravel backup command
%PHP_PATH% artisan backup:run

REM Run the cleanup command after backup
%PHP_PATH% artisan backup:clean

REM Optional: Log output to a file for troubleshooting
REM You should ensure the directory exists first (e.g., C:\Users\your_username\Documents\LaravelBackups\dukani)
REM %PHP_PATH% artisan backup:run > "%USERPROFILE%\Documents\LaravelBackups\dukani\backup_log.txt" 2>&1
REM %PHP_PATH% artisan backup:clean >> "%USERPROFILE%\Documents\LaravelBackups\dukani\backup_log.txt" 2>&1

echo Backup and cleanup process finished.