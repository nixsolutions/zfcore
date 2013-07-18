@echo off
set installDir=%CD%
echo %installDir%
copy /v %installDir%\public\.htaccess.sample %installDir%\public\.htaccess
echo Copy .htaccess file

cd %installDir%\bin\

echo Done

cd %installDir%

//Loading composer and dependencies
echo Downloading composer
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

echo Installing dependencie
php composer.phar install
