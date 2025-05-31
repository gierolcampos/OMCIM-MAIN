@echo off
echo Installing Intervention Image package...
composer require intervention/image

echo Creating storage link...
php artisan storage:link

echo Done! Profile picture functionality is now ready to use.
pause
