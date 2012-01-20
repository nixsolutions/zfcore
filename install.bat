@echo off
set installDir=%CD%

mkdir %installDir%\data\cache\
mkdir %installDir%\data\logs\
mkdir %installDir%\data\session\
mkdir %installDir%\data\uploads\

mkdir %installDir%\public\images\captcha\
mkdir  %installDir%\public\uploads\

cd %installDir%\bin\

zfc.bat up migration

cd %installDir%