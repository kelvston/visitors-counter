@echo off
cd C:\xampp\htdocs\visitors-counter
php artisan schedule:run >> NUL 2>&1
php artisan schedule:run >> C:\xampp\htdocs\visitors-counter\storage\logs\laravel.log 2>&1
