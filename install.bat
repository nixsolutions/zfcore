@echo off
set installDir=%CD%

cd %installDir%\bin\

zfc.bat up migration

cd %installDir%