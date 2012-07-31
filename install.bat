@echo off
set installDir=%CD%

copy /v %installDir%\public\.htaccess.sample %installDir%\public\.htaccess
echo Copy .htaccess file

cd %installDir%\bin\

echo Done

cd %installDir%